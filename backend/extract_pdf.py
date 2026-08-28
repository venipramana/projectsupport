#!/usr/bin/env python3
import sys
import re
import zlib
import json

def decompress_stream(data):
    if not data:
        return None
    for offset in range(min(15, len(data))):
        sub = data[offset:]
        try:
            return zlib.decompress(sub)
        except Exception:
            pass
        try:
            return zlib.decompress(sub, -zlib.MAX_WBITS)
        except Exception:
            pass
        try:
            d = zlib.decompressobj()
            res = d.decompress(sub)
            if res: return res
        except Exception:
            pass
        try:
            d = zlib.decompressobj(-zlib.MAX_WBITS)
            res = d.decompress(sub)
            if res: return res
        except Exception:
            pass
    return None

def decode_pdf_string(s):
    def oct_repl(m):
        try:
            return chr(int(m.group(1), 8))
        except Exception:
            return m.group(0)
    s = re.sub(r'\\([0-7]{1,3})', oct_repl, s)
    s = s.replace(r'\\', '\\').replace(r'\(', '(').replace(r'\)', ')').replace(r'\r', '\r').replace(r'\n', '\n').replace(r'\t', '\t').replace('\x00', '')
    return s

def decode_pdf_hex(h, cmaps={}):
    h_clean = re.sub(r'[^0-9a-fA-F]', '', h)
    if not h_clean:
        return ""
    h_upper = h_clean.upper()
    if h_upper in cmaps:
        return cmaps[h_upper]

    try:
        if len(h_clean) % 2 != 0:
            h_clean += '0'
        raw_bytes = bytes.fromhex(h_clean)

        if raw_bytes.startswith(b'\xfe\xff'):
            return raw_bytes[2:].decode('utf-16-be', errors='ignore').replace('\x00', '')

        if len(h_clean) >= 4 and len(h_clean) % 4 == 0:
            decoded = raw_bytes.decode('utf-16-be', errors='ignore')
            if decoded and any(c.isalnum() for c in decoded):
                return decoded.replace('\x00', '')

        return raw_bytes.decode('latin-1', errors='ignore').replace('\x00', '')
    except Exception:
        return ""

def parse_cmap(cmap_str):
    mapping = {}
    bfchar_matches = re.findall(r'<([0-9a-fA-F]+)>\s*<([0-9a-fA-F]+)>', cmap_str)
    for src, dst in bfchar_matches:
        try:
            val = "".join(chr(int(dst[i:i+4], 16)) for i in range(0, len(dst), 4))
            mapping[src.upper()] = val
        except Exception:
            pass
    bfrange_matches = re.findall(r'<([0-9a-fA-F]+)>\s*<([0-9a-fA-F]+)>\s*<([0-9a-fA-F]+)>', cmap_str)
    for src_start, src_end, dst_start in bfrange_matches:
        try:
            start = int(src_start, 16)
            end = int(src_end, 16)
            dst_base = int(dst_start, 16)
            hex_len = len(src_start)
            for i in range(start, end + 1):
                key = f"{i:0{hex_len}X}"
                val = chr(dst_base + (i - start))
                mapping[key] = val
        except Exception:
            pass
    return mapping

def extract_text_from_pdf_data(raw_data):
    text_chunks = []
    cmaps = {}

    pos = 0
    while True:
        s_idx = raw_data.find(b'stream', pos)
        if s_idx == -1:
            break
        e_idx = raw_data.find(b'endstream', s_idx)
        if e_idx == -1:
            break

        start_data = s_idx + 6
        stream_bytes = raw_data[start_data:e_idx]
        header = raw_data[max(0, s_idx - 500):s_idx].decode('latin-1', errors='ignore')
        pos = e_idx + 9

        data_bytes = stream_bytes
        decomp = decompress_stream(stream_bytes)
        if decomp:
            data_bytes = decomp

        try:
            decomp_str = data_bytes.decode('latin-1', errors='ignore')
        except Exception:
            continue

        if 'begincmap' in decomp_str:
            cmaps.update(parse_cmap(decomp_str))

        if '/Type /ObjStm' in header or '/ObjStm' in header:
            nested_pattern = re.compile(r'stream[\r\n]+(.*?)[\r\n]+endstream', re.DOTALL)
            for n_match in nested_pattern.finditer(decomp_str):
                text_chunks.append(n_match.group(1))

        # Extract text inside BT ... ET blocks
        bt_blocks = re.findall(r'BT(.*?)ET', decomp_str, re.DOTALL)
        if bt_blocks:
            for block in bt_blocks:
                strs = re.findall(r'\((.*?)\)', block, re.DOTALL)
                for s in strs:
                    decoded = decode_pdf_string(s)
                    if decoded.strip():
                        text_chunks.append(decoded)

                hexes = re.findall(r'<([0-9a-fA-F]{2,})>', block)
                for h in hexes:
                    decoded = decode_pdf_hex(h, cmaps)
                    if decoded.strip():
                        text_chunks.append(decoded)

        # Fallback Tj / TJ matching
        tj_matches = re.findall(r'\((.*?)\)\s*(?:Tj|\'|")', decomp_str, re.DOTALL)
        for t in tj_matches:
            text_chunks.append(decode_pdf_string(t))

        tj_arr_matches = re.findall(r'\[(.*?)\]\s*TJ', decomp_str, re.DOTALL)
        for arr in tj_arr_matches:
            sub_strs = re.findall(r'\((.*?)\)|<([0-9a-fA-F]+)>', arr, re.DOTALL)
            for s, h in sub_strs:
                if s:
                    text_chunks.append(decode_pdf_string(s))
                elif h:
                    decoded = decode_pdf_hex(h, cmaps)
                    if decoded.strip():
                        text_chunks.append(decoded)

    if not text_chunks:
        raw_str = raw_data.decode('latin-1', errors='ignore').replace('\x00', '')
        strs = re.findall(r'\(([A-Za-z0-9\s\:\/\.\,\_\-\(\)]+)\)', raw_str)
        for t in strs:
            text_chunks.append(t)

    full_text = " ".join(text_chunks)
    full_text = re.sub(r'\s+', ' ', full_text).strip()
    return full_text

def is_valid_nomor(candidate):
    if not candidate:
        return False
    cand = candidate.strip()
    if not re.search(r'\d{2,}', cand):
        return False
    if re.match(r'^(?:19|20)\d{2}$', cand):
        return False
    if re.match(r'^(?:Bandung|Jakarta|Surakarta|Semarang|Surabaya|Yogyakarta|Lampiran|Perihal|Kepada|Nota|Dinas)$', cand, re.IGNORECASE):
        return False
    return True

def parse_nde_text(text, filename=""):
    nomor = ""
    tanggal_str = ""
    perihal = ""

    # 1. Priority: Exact slash document number pattern (e.g. 74790/DS.02.03/VIII/2026)
    m_slash = (
        re.search(r'([0-9]{3,}\s*[\/\.-]\s*[A-Za-z0-9\.\_\-]+\s*[\/\.-]\s*[IVXLCDM0-9]+\s*[\/\.-]\s*[0-9]{2,4})', text, re.IGNORECASE) or
        re.search(r'([0-9]{3,}\s*[\/\.-]\s*[A-Za-z0-9\.\_\-]+\s*[\/\.-]\s*[0-9]{2,4})', text, re.IGNORECASE) or
        re.search(r'([0-9]{3,}\s*[\/\.-]\s*[A-Za-z0-9\.\_\-]+)', text, re.IGNORECASE)
    )
    if m_slash:
        cand = m_slash.group(1).strip()
        cand = re.sub(r'\s*\/\s*', '/', cand)
        if is_valid_nomor(cand):
            nomor = cand

    # 2. Priority: Nomor header matching
    if not nomor:
        m_nomor = (
            re.search(r'Nomor\s*[:\=]?\s*([0-9A-Za-z\/\.\_\-\s]+?)(?=\s+(?:Lampiran|Perihal|Kepada|Bandung|Jakarta|$))', text, re.IGNORECASE) or
            re.search(r'(?:Nomor|No\.?|ND|Nota\s+Dinas)\s*[:\=]?\s*([0-9A-Za-z\/\.\_\-\s]+)', text, re.IGNORECASE)
        )
        if m_nomor:
            cand = m_nomor.group(1).strip()
            cand = re.sub(r'\s+(?:Lampiran|Perihal|Kepada|Bandung|Jakarta).*$', '', cand, flags=re.IGNORECASE).strip()
            cand = re.sub(r'\s*\/\s*', '/', cand)
            if is_valid_nomor(cand):
                nomor = cand

    # Tanggal
    m_tgl = (
        re.search(r'(?:Bandung|Jakarta|Surakarta|Semarang|Surabaya|Yogyakarta|[\w\s]+)?,\s*(\d{1,2}\s+[A-Za-z]+\s+\d{4})', text, re.IGNORECASE) or
        re.search(r'(\d{1,2}\s+(?:Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+\d{4})', text, re.IGNORECASE) or
        re.search(r'(\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{4})', text)
    )
    if m_tgl:
        tanggal_str = m_tgl.group(1).strip()

    # Perihal
    m_perihal = (
        re.search(r'Perihal\s*[:\=]?\s*([^\r\n]+?)(?=\s+(?:Kepada|Menunjuk|Dengan|Sehubungan|Lampiran|Diberitahukan|Bandung|Jakarta|Surakarta|Semarang|Surabaya|Yogyakarta|\d{1,2}\s+[A-Za-z]+\s+\d{4}|1\.|2\.|3\.|$))', text, re.IGNORECASE) or
        re.search(r'Perihal\s*[:\=]?\s*([^\r\n]+)', text, re.IGNORECASE)
    )
    if m_perihal:
        perihal = m_perihal.group(1).strip()
        perihal = re.sub(r'\s+(?:Kepada|Bandung|Jakarta|Surakarta|Semarang|Surabaya|Yogyakarta|Diberitahukan):?.*$', '', perihal, flags=re.IGNORECASE).strip()

    # Clean filename
    clean_filename = re.sub(r'\.[^/.]+$', '', filename) if filename else ""

    # Fallback for perihal
    if not perihal and clean_filename:
        perihal = clean_filename

    # Fallback for Nomor & Tanggal from filename if missing
    if not nomor and clean_filename:
        m_fn_nomor = (
            re.search(r'([0-9]{3,}[\s\_\/\.-]+[A-Za-z0-9\.\_\-]+[\s\_\/\.-]+[IVXLCDM0-9]+[\s\_\/\.-]+[0-9]{2,4})', clean_filename, re.IGNORECASE) or
            re.search(r'([0-9]{3,}[\s\_\/\.-]+[A-Za-z0-9\.\_\-]+[\s\_\/\.-]+[0-9]{2,4})', clean_filename, re.IGNORECASE) or
            re.search(r'([0-9]{3,}[\s\_\/\.-]+[A-Za-z0-9\.\_\-]+)', clean_filename, re.IGNORECASE) or
            re.search(r'([0-9]{4,})', clean_filename)
        )
        if m_fn_nomor:
            candidate = m_fn_nomor.group(1).strip()
            candidate = re.sub(r'[\s\_]+', '/', candidate)
            if is_valid_nomor(candidate):
                nomor = candidate

    if not tanggal_str and clean_filename:
        m_fn_tgl = re.search(r'(\d{1,2}\s+(?:Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+\d{4})', clean_filename, re.IGNORECASE) or \
                   re.search(r'(\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{4})', clean_filename)
        if m_fn_tgl:
            tanggal_str = m_fn_tgl.group(1).strip()

    # If tanggal_str is STILL empty, use current date
    if not tanggal_str:
        import datetime
        now = datetime.datetime.now()
        months_id = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
        tanggal_str = f"{now.day} {months_id[now.month-1]} {now.year}"

    months = {
        'januari':'01', 'februari':'02', 'maret':'03', 'april':'04',
        'mei':'05', 'juni':'06', 'juli':'07', 'agustus':'08',
        'september':'09', 'oktober':'10', 'november':'11', 'desember':'12'
    }

    formatted_tanggal = ""
    m_dmy = re.search(r'(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})', tanggal_str)
    if m_dmy:
        day = m_dmy.group(1).zfill(2)
        m_name = m_dmy.group(2).lower()
        year = m_dmy.group(3)
        if m_name in months:
            formatted_tanggal = f"{day}-{months[m_name]}-{year}"

    if not formatted_tanggal:
        import datetime
        formatted_tanggal = datetime.datetime.now().strftime("%d-%m-%Y")

    # Format Catatan
    catatan_parts = ["NDE"]
    if nomor:
        catatan_parts.append(nomor)
    else:
        catatan_parts.append("[Nomor]")

    if tanggal_str:
        catatan_parts.append(f"tanggal {tanggal_str}")
    if perihal:
        catatan_parts.append(f": {perihal}")

    formatted_catatan = " ".join(catatan_parts)

    return {
        "success": True,
        "text": text,
        "nomor": nomor,
        "tanggal": tanggal_str,
        "perihal": perihal,
        "formatted_catatan": formatted_catatan,
        "formatted_tanggal": formatted_tanggal
    }

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No file path provided"}))
        sys.exit(1)

    file_path = sys.argv[1]
    filename = sys.argv[2] if len(sys.argv) > 2 else ""

    try:
        with open(file_path, "rb") as f:
            raw_data = f.read()
        extracted_text = extract_text_from_pdf_data(raw_data)
        result = parse_nde_text(extracted_text, filename)
        print(json.dumps(result))
    except Exception as e:
        print(json.dumps({"error": str(e)}))
