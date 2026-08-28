<?php

namespace App\Http\Controllers;

use App\Models\Hproject;
use App\Models\Project;
use App\Models\Rproject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HprojectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($idproject)
    {
        $project = Project::with('direktorat_rel', 'rkap_rel')->findOrFail($idproject);
        
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }
        
        $hprojects = Hproject::with('rproject_rel')
            ->where('idproject', $idproject)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $rprojects = Rproject::all();

        $evidenceFiles = [];
        if (!empty($project->folder_evidence)) {
            try {
                $disk = Storage::disk('minio');
                $files = $disk->files($project->folder_evidence);
                foreach ($files as $file) {
                    $fileName = basename($file);
                    if ($fileName === '.keep') continue;
                    
                    $size = $disk->size($file);
                    $sizeStr = $size >= 1048576 
                        ? round($size / 1048576, 2) . ' MB' 
                        : ($size >= 1024 ? round($size / 1024, 2) . ' KB' : $size . ' B');

                    $lastModified = $disk->lastModified($file);

                    $evidenceFiles[] = [
                        'name' => $fileName,
                        'path' => $file,
                        'size' => $sizeStr,
                        'date' => date('d-m-Y H:i', $lastModified),
                    ];
                }
            } catch (\Exception $e) {
                Log::error("MinIO error listing evidence: " . $e->getMessage());
            }
        }

        return view('hproject.index', compact('project', 'hprojects', 'rprojects', 'evidenceFiles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'idproject' => 'required|integer',
            'rproject' => 'required|integer',
            'tanggal' => 'required|string',
            'catatan' => 'required|string|max:500',
            'progress' => 'nullable|integer',
            'progress_file' => 'nullable|file|max:51200'
        ]);

        $project = Project::findOrFail($request->idproject);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }

        $data = $request->except(['progress_file']);
        
        // Convert dd-mm-yyyy to YYYYMMDD
        if (!empty($data['tanggal'])) {
            try {
                $data['tanggal'] = Carbon::createFromFormat('d-m-Y', $data['tanggal'])->format('Ymd');
            } catch (\Exception $e) {
                // Ignore formatting error, let DB handle
            }
        }

        Hproject::create($data);

        // Upload progress file directly to MinIO if present
        $uploadedFileMsg = '';
        if ($request->hasFile('progress_file')) {
            try {
                if (empty($project->folder_evidence)) {
                    $project->folder_evidence = "evidence_project_" . $project->id;
                    $project->save();
                }

                $disk = Storage::disk('minio');
                if (!$disk->exists($project->folder_evidence . '/.keep')) {
                    $disk->put($project->folder_evidence . '/.keep', '');
                }

                $file = $request->file('progress_file');
                $filename = $file->getClientOriginalName();
                $disk->putFileAs($project->folder_evidence, $file, $filename);
                $uploadedFileMsg = " Dokumen progress (\"{$filename}\") berhasil di-upload ke MinIO.";
            } catch (\Exception $e) {
                Log::error("MinIO upload error on hproject.store: " . $e->getMessage());
            }
        }

        // Update parent project status
        $project = Project::find($data['idproject']);
        if ($project) {
            $project->update(['rproject' => $data['rproject']]);
        }

        return redirect()->route('hproject.index', $data['idproject'])
            ->with('success', 'Progress project berhasil ditambahkan.' . $uploadedFileMsg);
    }

    /**
     * Parse NDE file via AJAX to extract Nomor, Tanggal, Perihal.
     */
    /**
     * Parse NDE file via AJAX to extract Nomor, Tanggal, Perihal.
     */
    public function parseNde(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200'
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $text = '';

        if ($extension === 'docx') {
            try {
                $zip = new \ZipArchive();
                if ($zip->open($file->getRealPath()) === true) {
                    $xmlContent = $zip->getFromName('word/document.xml');
                    $zip->close();
                    if ($xmlContent) {
                        $text = strip_tags(str_replace(['</w:p>', '</w:tr>', '<w:tab/>', '<w:br/>'], ["\n", "\n", "\t", "\n"], $xmlContent));
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error parsing docx: " . $e->getMessage());
            }
        } elseif ($extension === 'pdf') {
            try {
                // 1. Try Python PDF Extractor Helper first
                $pythonScript = base_path('extract_pdf.py');
                if (file_exists($pythonScript)) {
                    $cmd = "python3 " . escapeshellarg($pythonScript) . " " . escapeshellarg($file->getRealPath()) . " " . escapeshellarg($file->getClientOriginalName());
                    $output = shell_exec($cmd);
                    if ($output) {
                        $json = json_decode($output, true);
                        if (!empty($json) && isset($json['success']) && $json['success']) {
                            return response()->json($json);
                        }
                    }
                }
                // 2. Fallback to PHP native PDF stream text extractor
                $text = $this->extractTextFromPdf($file->getRealPath());
            } catch (\Exception $e) {
                Log::error("Error parsing pdf: " . $e->getMessage());
            }
        }

        $nomor = '';
        $tanggalStr = '';
        $perihal = '';

        if (!empty($text)) {
            // 1. Extract Nomor (e.g. 74790/DS.02.03/VIII/2026 or 81519/DS.02/VIII/2026 or Nomor : ...)
            if (preg_match('/Nomor\s*[:\=]?\s*([0-9A-Za-z\/\.\_\-]+)/i', $text, $m)) {
                $nomor = trim($m[1]);
            } elseif (preg_match('/([0-9]{3,}\/[A-Za-z0-9\.\_\-]+\/[IVXLCDM]+\/[0-9]{4})/i', $text, $m)) {
                $nomor = trim($m[1]);
            } elseif (preg_match('/Nomor\s*[:\=]?\s*([^\r\n]+?)(?=\s+(?:Lampiran|Perihal|Kepada|Bandung|Jakarta|$))/i', $text, $m)) {
                $nomor = trim($m[1]);
            }

            // 2. Extract Tanggal (e.g. Bandung, 6 Agustus 2026 or 6 Agustus 2026)
            if (preg_match('/(?:Bandung|Jakarta|Surakarta|Semarang|Surabaya|Yogyakarta|[\w\s]+)?,\s*(\d{1,2}\s+[A-Za-z]+\s+\d{4})/i', $text, $m)) {
                $tanggalStr = trim($m[1]);
            } elseif (preg_match('/(\d{1,2}\s+(?:Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+\d{4})/i', $text, $m)) {
                $tanggalStr = trim($m[1]);
            } elseif (preg_match('/(\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{4})/', $text, $m)) {
                $tanggalStr = trim($m[1]);
            }

            // 3. Extract Perihal (e.g. Perihal : ...)
            if (preg_match('/Perihal\s*[:\=]?\s*([^\r\n]+?)(?=\s+(?:Kepada|Menunjuk|Dengan|Sehubungan|Lampiran|Diberitahukan|1\.|2\.|3\.|$))/i', $text, $m)) {
                $perihal = trim($m[1]);
            } elseif (preg_match('/Perihal\s*[:\=]?\s*([^\r\n]+)/i', $text, $m)) {
                $perihal = trim($m[1]);
            }

            // Clean perihal trailing text if matched prematurely
            if (!empty($perihal)) {
                $perihal = preg_replace('/\s+Kepada:?.*$/i', '', $perihal);
                $perihal = trim($perihal);
            }
        }

        // Fallback for perihal from clean filename
        if (empty($perihal) && !empty($originalName)) {
            $perihal = $originalName;
        }

        // Fallback for Nomor & Tanggal from filename
        if (empty($nomor) && !empty($originalName)) {
            if (preg_match('/([0-9]{3,}[\s\_\/\.-]+[A-Za-z0-9\.\_\-]+[\s\_\/\.-]+[IVXLCDM0-9]+[\s\_\/\.-]+[0-9]{2,4})/i', $originalName, $m) ||
                preg_match('/([0-9]{3,}[\s\_\/\.-]+[A-Za-z0-9\.\_\-]+[\s\_\/\.-]+[0-9]{2,4})/i', $originalName, $m) ||
                preg_match('/([0-9]{3,}[\s\_\/\.-]+[A-Za-z0-9\.\_\-]+)/i', $originalName, $m)) {
                $cand = trim($m[1]);
                $cand = preg_replace('/[\s\_]+/', '/', $cand);
                if (!preg_match('/^(?:19|20)\d{2}$/', $cand) && preg_match('/\d{2,}/', $cand)) {
                    $nomor = $cand;
                }
            }
        }

        if (empty($tanggalStr) && !empty($originalName)) {
            if (preg_match('/(\d{1,2}\s+(?:Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+\d{4})/i', $originalName, $m)) {
                $tanggalStr = trim($m[1]);
            }
        }

        // Convert tanggal to dd-mm-yyyy format if recognized, else fallback to today
        $formattedTanggal = '';
        if ($tanggalStr) {
            $formattedTanggal = $this->parseIndonesianDateToDMY($tanggalStr);
        }
        if (empty($formattedTanggal)) {
            $formattedTanggal = date('d-m-Y');
            if (empty($tanggalStr)) {
                $monthsId = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                $tanggalStr = date('j') . ' ' . $monthsId[(int)date('n')] . ' ' . date('Y');
            }
        }

        // Clean Catatan formatting
        $catatanParts = ["NDE"];
        if (!empty($nomor)) {
            $catatanParts[] = $nomor;
        } else {
            $catatanParts[] = "[Nomor]";
        }

        if (!empty($tanggalStr)) {
            $catatanParts[] = "tanggal " . $tanggalStr;
        }
        if (!empty($perihal)) {
            $catatanParts[] = ": " . $perihal;
        }

        $formattedCatatan = implode(" ", $catatanParts);

        return response()->json([
            'success' => true,
            'text' => $text,
            'nomor' => $nomor,
            'tanggal' => $tanggalStr,
            'perihal' => $perihal,
            'formatted_catatan' => $formattedCatatan,
            'formatted_tanggal' => $formattedTanggal,
        ]);
    }

    /**
     * Decompress and extract text tokens from PDF streams.
     */
    private function extractTextFromPdf($filePath)
    {
        if (!file_exists($filePath)) return '';
        $content = file_get_contents($filePath);
        if (!$content) return '';

        $text = '';

        // Find all stream blocks in PDF
        preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $content, $streamMatches, PREG_OFFSET_CAPTURE);

        foreach ($streamMatches[1] as $idx => $match) {
            $rawStream = $match[0];
            $offset = $streamMatches[0][$idx][1];

            // Check header before stream for compression filters
            $header = substr($content, max(0, $offset - 350), 350);
            $isFlate = (strpos($header, '/FlateDecode') !== false || strpos($header, '/Fl') !== false);

            $data = $rawStream;
            if ($isFlate) {
                $decompressed = @gzuncompress($rawStream);
                if ($decompressed === false) {
                    $decompressed = @gzinflate($rawStream);
                }
                if ($decompressed === false) {
                    $decompressed = @zlib_decode($rawStream);
                }
                if ($decompressed !== false) {
                    $data = $decompressed;
                }
            }

            // Extract (text) Tj
            preg_match_all('/\((.*?)\)\s*Tj/s', $data, $tjMatches);
            foreach ($tjMatches[1] as $t) {
                $text .= $this->decodePdfString($t) . " ";
            }

            // Extract [(text)] TJ
            preg_match_all('/\[(.*?)\]\s*TJ/s', $data, $tjArrMatches);
            foreach ($tjArrMatches[1] as $arrContent) {
                preg_match_all('/\((.*?)\)|<([0-9a-fA-F]+)>/s', $arrContent, $subMatches);
                foreach ($subMatches[1] as $idx2 => $str) {
                    if ($str !== '') {
                        $text .= $this->decodePdfString($str) . " ";
                    } elseif (!empty($subMatches[2][$idx2])) {
                        $text .= $this->decodePdfHex($subMatches[2][$idx2]) . " ";
                    }
                }
            }

            // Extract <hex> Tj
            preg_match_all('/<([0-9a-fA-F]+)>\s*Tj/s', $data, $hexTjMatches);
            foreach ($hexTjMatches[1] as $hex) {
                $text .= $this->decodePdfHex($hex) . " ";
            }
        }

        // Fallback for uncompressed PDF text strings outside stream blocks
        if (trim($text) === '') {
            preg_match_all('/\((.*?)\)\s*Tj/s', $content, $tjMatches);
            foreach ($tjMatches[1] as $t) {
                $text .= $this->decodePdfString($t) . " ";
            }
        }

        return trim(preg_replace('/\s+/', ' ', $text));
    }

    private function decodePdfString($str)
    {
        $str = preg_replace_callback('/\\\\([0-7]{1,3})/', function($m) {
            return chr(octdec($m[1]));
        }, $str);
        $clean = str_replace(
            ['\\\\', '\\(', '\\)', '\\r', '\\n', '\\t', '\\b', '\\f', "\x00"],
            ['\\', '(', ')', "\r", "\n", "\t", "\b", "\f", ''],
            $str
        );
        return $clean;
    }

    private function decodePdfHex($hex)
    {
        $hex = preg_replace('/[^0-9a-fA-F]/', '', $hex);
        if (empty($hex)) return '';
        if (strlen($hex) % 2 !== 0) $hex .= '0';
        $bin = @hex2bin($hex);
        if ($bin === false) return '';

        if (strpos($bin, "\xFE\xFF") === 0) {
            return str_replace("\x00", '', @mb_convert_encoding(substr($bin, 2), 'UTF-8', 'UTF-16BE') ?: '');
        }

        if (strlen($hex) >= 4 && strlen($hex) % 4 === 0) {
            $utf8 = @mb_convert_encoding($bin, 'UTF-8', 'UTF-16BE');
            if ($utf8 !== false && preg_match('//u', $utf8)) {
                $clean = str_replace("\x00", '', $utf8);
                if (trim($clean) !== '') {
                    return $clean;
                }
            }
        }

        return str_replace("\x00", '', $bin);
    }

    private function parseIndonesianDateToDMY($dateStr)
    {
        $months = [
            'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
            'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
            'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12'
        ];

        if (preg_match('/(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})/', $dateStr, $m)) {
            $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $monthName = strtolower($m[2]);
            $year = $m[3];

            if (isset($months[$monthName])) {
                return "{$day}-{$months[$monthName]}-{$year}";
            }
        }
        return '';
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'rproject' => 'required|integer',
            'tanggal' => 'required|string',
            'catatan' => 'required|string|max:150',
            'progress' => 'nullable|integer'
        ]);

        $hproject = Hproject::findOrFail($id);
        $project = Project::findOrFail($hproject->idproject);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }

        $data = $request->all();

        // Convert dd-mm-yyyy to YYYYMMDD
        if (!empty($data['tanggal'])) {
            try {
                $data['tanggal'] = Carbon::createFromFormat('d-m-Y', $data['tanggal'])->format('Ymd');
            } catch (\Exception $e) {
                // Ignore formatting error, let DB handle
            }
        }

        $hproject->update($data);

        // Update parent project status
        $project = Project::find($hproject->idproject);
        if ($project) {
            $project->update(['rproject' => $data['rproject']]);
        }

        return redirect()->route('hproject.index', $hproject->idproject)->with('success', 'Progress project berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $hproject = Hproject::findOrFail($id);
        $project = Project::findOrFail($hproject->idproject);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }
        $idproject = $hproject->idproject;
        $hproject->delete();

        return redirect()->route('hproject.index', $idproject)->with('success', 'Progress project berhasil dihapus.');
    }

    /**
     * Store multiple uploaded evidence files to MinIO.
     */
    public function storeEvidence(Request $request, $project_id)
    {
        $project = Project::findOrFail($project_id);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'evidences' => 'required|array',
            'evidences.*' => 'required|file|max:51200' // Maks 50MB per file
        ]);

        if (empty($project->folder_evidence)) {
            $project->folder_evidence = "evidence_project_" . $project->id;
            $project->save();
        }

        $disk = Storage::disk('minio');
        if (!$disk->exists($project->folder_evidence . '/.keep')) {
            $disk->put($project->folder_evidence . '/.keep', '');
        }

        $uploadCount = 0;
        if ($request->hasFile('evidences')) {
            foreach ($request->file('evidences') as $file) {
                $filename = $file->getClientOriginalName();
                $disk->putFileAs($project->folder_evidence, $file, $filename);
                $uploadCount++;
            }
        }

        return redirect()->route('hproject.index', $project_id)
            ->with('success', "Berhasil mengupload {$uploadCount} file evidence ke MinIO.");
    }

    /**
     * Stream evidence file from MinIO for viewing in browser.
     */
    public function viewEvidence($project_id, $filename)
    {
        $project = Project::findOrFail($project_id);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }

        $path = $project->folder_evidence . '/' . $filename;
        $disk = Storage::disk('minio');

        if (!$disk->exists($path)) {
            abort(404, 'File evidence tidak ditemukan di MinIO.');
        }

        return $disk->response($path);
    }

    /**
     * Download evidence file from MinIO.
     */
    public function downloadEvidence($project_id, $filename)
    {
        $project = Project::findOrFail($project_id);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }

        $path = $project->folder_evidence . '/' . $filename;
        $disk = Storage::disk('minio');

        if (!$disk->exists($path)) {
            abort(404, 'File evidence tidak ditemukan di MinIO.');
        }

        return $disk->download($path, $filename);
    }

    /**
     * Delete evidence file from MinIO.
     */
    public function destroyEvidence($project_id, $filename)
    {
        $project = Project::findOrFail($project_id);
        if (auth()->user()->levelpengguna == 3 && $project->leadby != auth()->user()->idpengguna) {
            abort(403, 'Unauthorized.');
        }

        $path = $project->folder_evidence . '/' . $filename;
        $disk = Storage::disk('minio');

        if ($disk->exists($path)) {
            $disk->delete($path);

            $remainingFiles = $disk->files($project->folder_evidence);
            if (empty($remainingFiles) || (count($remainingFiles) === 1 && basename($remainingFiles[0]) === '.keep')) {
                if (!$disk->exists($project->folder_evidence . '/.keep')) {
                    $disk->put($project->folder_evidence . '/.keep', '');
                }
            }

            return redirect()->route('hproject.index', $project_id)
                ->with('success', "File evidence \"{$filename}\" berhasil dihapus dari MinIO.");
        }

        return redirect()->route('hproject.index', $project_id)
            ->with('error', "File evidence tidak ditemukan di storage.");
    }
}
