import os
import math
from PIL import Image, ImageDraw, ImageFilter

OUTPUT_DIR = '/home/vipi/workspace/projects/pm/backend/public/icons'
STEP_DIR = '/home/vipi/workspace/projects/pm/backend/public/icons/step'

S = 512  # Render size for high-quality supersampling
FINAL_SIZE = 256
CX, CY = S // 2, S // 2
RADIUS = 210

def draw_base_badge(grad_start, grad_end, shadow_opacity=80):
    img = Image.new('RGBA', (S, S), (0, 0, 0, 0))
    
    # Soft drop shadow
    shadow = Image.new('RGBA', (S, S), (0, 0, 0, 0))
    sdraw = ImageDraw.Draw(shadow)
    sdraw.ellipse([CX - RADIUS, CY - RADIUS + 16, CX + RADIUS, CY + RADIUS + 16], fill=(0, 0, 0, shadow_opacity))
    shadow = shadow.filter(ImageFilter.GaussianBlur(22))
    img.paste(shadow, (0, 0), shadow)
    
    # Circle mask
    circle_mask = Image.new('L', (S, S), 0)
    cdraw = ImageDraw.Draw(circle_mask)
    cdraw.ellipse([CX - RADIUS, CY - RADIUS, CX + RADIUS, CY + RADIUS], fill=255)
    
    # Diagonal gradient
    grad = Image.new('RGBA', (S, S), (0, 0, 0, 0))
    for y in range(S):
        for x in range(S):
            t = (x + y) / (2.0 * S)
            r = int(grad_start[0] + (grad_end[0] - grad_start[0]) * t)
            g = int(grad_start[1] + (grad_end[1] - grad_start[1]) * t)
            b = int(grad_start[2] + (grad_end[2] - grad_start[2]) * t)
            grad.putpixel((x, y), (r, g, b, 255))
            
    circle_colored = Image.composite(grad, Image.new('RGBA', (S, S), (0, 0, 0, 0)), circle_mask)
    img.paste(circle_colored, (0, 0), circle_colored)
    
    # Inner rim highlight (glass effect)
    rim = Image.new('RGBA', (S, S), (0, 0, 0, 0))
    rdraw = ImageDraw.Draw(rim)
    rdraw.ellipse([CX - RADIUS + 4, CY - RADIUS + 4, CX + RADIUS - 4, CY + RADIUS - 4], outline=(255, 255, 255, 85), width=6)
    img.paste(rim, (0, 0), rim)
    
    return img

def save_icon(img, filename):
    downsampled = img.resize((FINAL_SIZE, FINAL_SIZE), Image.Resampling.LANCZOS)
    out_path = os.path.join(OUTPUT_DIR, filename)
    downsampled.save(out_path, 'PNG', optimize=True)
    step_path = os.path.join(STEP_DIR, filename)
    if os.path.exists(STEP_DIR):
        downsampled.save(step_path, 'PNG', optimize=True)
    print(f"Saved: {filename}")

# 1. DEVELOPMENT: Modern Code Brackets & Slash
# Gradient: Vibrant Blue to Electric Cyan
def create_development():
    img = draw_base_badge((30, 90, 240), (6, 182, 212))
    draw = ImageDraw.Draw(img)
    bw = 30
    # < bracket
    draw.line([(CX - 45, CY - 85), (CX - 120, CY), (CX - 45, CY + 85)], fill=(255, 255, 255, 255), width=bw, joint='curve')
    # / slash
    draw.line([(CX + 35, CY - 105), (CX - 35, CY + 105)], fill=(255, 255, 255, 230), width=bw - 4)
    # > bracket
    draw.line([(CX + 45, CY - 85), (CX + 120, CY), (CX + 45, CY + 85)], fill=(255, 255, 255, 255), width=bw, joint='curve')
    save_icon(img, 'development.png')

# 2. INTERNAL TEST: Checklist Clipboard with Checkmarks
# Gradient: Sunset Orange to Golden Amber
def create_internaltest():
    img = draw_base_badge((234, 88, 12), (245, 158, 11))
    draw = ImageDraw.Draw(img)
    
    # Clipboard board
    cw, ch = 190, 240
    c_left = CX - cw // 2
    c_top = CY - ch // 2 + 15
    draw.rounded_rectangle([c_left, c_top, c_left + cw, c_top + ch], radius=24, outline=(255, 255, 255, 255), width=18)
    
    # Clip on top
    clip_w, clip_h = 76, 28
    draw.rounded_rectangle([CX - clip_w // 2, c_top - 14, CX + clip_w // 2, c_top + clip_h - 14], radius=10, fill=(255, 255, 255, 255))
    
    # Checklist rows
    row_y = [c_top + 55, c_top + 115, c_top + 175]
    for idx, ry in enumerate(row_y):
        # Checkmark
        draw.line([(c_left + 32, ry), (c_left + 46, ry + 14), (c_left + 72, ry - 14)], fill=(255, 255, 255, 255), width=14, joint='curve')
        # Line
        line_left = c_left + 90
        line_w = 64 if idx == 1 else 72
        draw.rounded_rectangle([line_left, ry - 4, line_left + line_w, ry + 6], radius=4, fill=(255, 255, 255, 220))
        
    save_icon(img, 'internaltest.png')

# 3. QA / UAT: Shield of Verification & Quality
# Gradient: Royal Violet to Radiant Purple
def create_uat():
    img = draw_base_badge((124, 58, 237), (168, 85, 247))
    draw = ImageDraw.Draw(img)
    
    # Shield outline
    shield_pts = [
        (CX, CY - 110),
        (CX + 95, CY - 90),
        (CX + 85, CY + 30),
        (CX, CY + 120),
        (CX - 85, CY + 30),
        (CX - 95, CY - 90)
    ]
    draw.polygon(shield_pts, outline=(255, 255, 255, 255), fill=None)
    # Thicken outline
    for i in range(len(shield_pts)):
        p1 = shield_pts[i]
        p2 = shield_pts[(i + 1) % len(shield_pts)]
        draw.line([p1, p2], fill=(255, 255, 255, 255), width=20, joint='curve')
        
    # Big Checkmark inside shield
    draw.line([(CX - 45, CY), (CX - 12, CY + 36), (CX + 52, CY - 36)], fill=(255, 255, 255, 255), width=26, joint='curve')
    save_icon(img, 'uat.png')

# 4. DEPLOYMENT: Cloud Upload & Rocket Boost
# Gradient: Sky Blue to Indigo
def create_deployment():
    img = draw_base_badge((14, 116, 244), (99, 102, 241))
    draw = ImageDraw.Draw(img)
    
    # Rocket body
    rw = 22
    # Rocket nose cone: (CX, CY - 110)
    # Rocket base: (CX, CY + 60)
    rocket_body = [
        (CX, CY - 115),
        (CX + 45, CY - 30),
        (CX + 45, CY + 55),
        (CX - 45, CY + 55),
        (CX - 45, CY - 30)
    ]
    draw.polygon(rocket_body, fill=(255, 255, 255, 255))
    
    # Left fin
    draw.polygon([(CX - 45, CY + 20), (CX - 85, CY + 75), (CX - 45, CY + 65)], fill=(255, 255, 255, 255))
    # Right fin
    draw.polygon([(CX + 45, CY + 20), (CX + 85, CY + 75), (CX + 45, CY + 65)], fill=(255, 255, 255, 255))
    
    # Rocket window
    draw.ellipse([CX - 20, CY - 40, CX + 20, CY], fill=(30, 90, 240, 255))
    draw.ellipse([CX - 15, CY - 35, CX + 15, CY - 5], fill=(255, 255, 255, 255))
    
    # Thrust flame
    draw.polygon([(CX - 22, CY + 65), (CX, CY + 115), (CX + 22, CY + 65)], fill=(254, 215, 64, 255))
    draw.polygon([(CX - 12, CY + 65), (CX, CY + 95), (CX + 12, CY + 65)], fill=(255, 255, 255, 255))
    
    save_icon(img, 'deployment.png')

# 5. PILOTING: Navigation Compass / Aeroplane
# Gradient: Deep Teal to Emerald Cyan
def create_piloting():
    img = draw_base_badge((15, 118, 110), (14, 165, 233))
    draw = ImageDraw.Draw(img)
    
    # Paper plane / Pilot jet soaring upwards-right
    plane_pts = [
        (CX + 85, CY - 85),   # Nose (top right)
        (CX - 90, CY - 10),   # Left wingtip
        (CX - 25, CY + 25),   # Central body fold
        (CX - 50, CY + 90),   # Left fin
        (CX + 10, CY + 45),   # Right fold
    ]
    # Draw supersonic paper plane
    draw.polygon([(CX + 95, CY - 95), (CX - 85, CY - 5), (CX + 15, CY + 60)], fill=(255, 255, 255, 255))
    draw.polygon([(CX + 95, CY - 95), (CX + 15, CY + 60), (CX + 60, CY - 15)], fill=(225, 240, 255, 255))
    draw.polygon([(CX - 10, CY + 25), (CX - 35, CY + 85), (CX + 15, CY + 60)], fill=(185, 215, 250, 255))
    
    save_icon(img, 'piloting.png')

# 6. PRODUCTION / LIVE: Live Signal Globe / Glowing Checkmark
# Gradient: Rich Emerald to Vibrant Mint
def create_production():
    img = draw_base_badge((5, 150, 105), (16, 185, 129))
    draw = ImageDraw.Draw(img)
    
    # Double checkmark / Bold Live Check
    # Outer circular live ring (dashed/segmented)
    r_ring = 135
    draw.arc([CX - r_ring, CY - r_ring, CX + r_ring, CY + r_ring], start=0, end=360, fill=(255, 255, 255, 120), width=12)
    
    # Large pristine checkmark
    draw.line([(CX - 65, CY + 5), (CX - 15, CY + 55), (CX + 75, CY - 45)], fill=(255, 255, 255, 255), width=32, joint='curve')
    
    save_icon(img, 'production.png')

# 7. ON HOLD: Pause / Amber Clock
# Gradient: Warm Amber to Gold
def create_onhold():
    img = draw_base_badge((217, 119, 6), (245, 158, 11))
    draw = ImageDraw.Draw(img)
    
    bar_w, bar_h = 36, 150
    spacing = 26
    # Left bar
    draw.rounded_rectangle([CX - spacing - bar_w, CY - bar_h // 2, CX - spacing, CY + bar_h // 2], radius=16, fill=(255, 255, 255, 255))
    # Right bar
    draw.rounded_rectangle([CX + spacing, CY - bar_h // 2, CX + spacing + bar_w, CY + bar_h // 2], radius=16, fill=(255, 255, 255, 255))
    save_icon(img, 'onhold.png')

# 8. REJECT: Modern Decline Cross
# Gradient: Crimson to Coral Red
def create_reject():
    img = draw_base_badge((220, 38, 38), (244, 63, 94))
    draw = ImageDraw.Draw(img)
    
    cw = 32
    d = 75
    draw.line([(CX - d, CY - d), (CX + d, CY + d)], fill=(255, 255, 255, 255), width=cw, joint='curve')
    draw.line([(CX + d, CY - d), (CX - d, CY + d)], fill=(255, 255, 255, 255), width=cw, joint='curve')
    save_icon(img, 'reject.png')

# 9. REQUIREMENT: Spec Document with Notes
# Gradient: Slate to Steel Blue
def create_requirement():
    img = draw_base_badge((71, 85, 105), (100, 116, 139))
    draw = ImageDraw.Draw(img)
    
    dw, dh = 180, 230
    d_left = CX - dw // 2
    d_top = CY - dh // 2
    draw.rounded_rectangle([d_left, d_top, d_left + dw, d_top + dh], radius=22, outline=(255, 255, 255, 255), width=18)
    
    # Folded corner
    fold = 45
    draw.polygon([(d_left + dw - fold, d_top), (d_left + dw, d_top + fold), (d_left + dw - fold, d_top + fold)], fill=(255, 255, 255, 255))
    
    # Text lines
    lines = [d_top + 65, d_top + 110, d_top + 155]
    for idx, ly in enumerate(lines):
        w = 110 if idx < 2 else 75
        draw.rounded_rectangle([d_left + 35, ly - 5, d_left + 35 + w, ly + 5], radius=4, fill=(255, 255, 255, 230))
        
    save_icon(img, 'requirement.png')

# 10. SECURITY TEST: Cyber Shield & Keyhole
# Gradient: Deep Teal / Emerald
def create_security_test():
    img = draw_base_badge((15, 118, 110), (16, 185, 129))
    draw = ImageDraw.Draw(img)
    
    # Shield
    shield_pts = [
        (CX, CY - 110),
        (CX + 90, CY - 90),
        (CX + 80, CY + 30),
        (CX, CY + 120),
        (CX - 80, CY + 30),
        (CX - 90, CY - 90)
    ]
    for i in range(len(shield_pts)):
        p1 = shield_pts[i]
        p2 = shield_pts[(i + 1) % len(shield_pts)]
        draw.line([p1, p2], fill=(255, 255, 255, 255), width=18, joint='curve')
        
    # Keyhole
    draw.ellipse([CX - 22, CY - 35, CX + 22, CY + 5], fill=(255, 255, 255, 255))
    draw.polygon([(CX - 15, CY - 5), (CX + 15, CY - 5), (CX + 20, CY + 45), (CX - 20, CY + 45)], fill=(255, 255, 255, 255))
    save_icon(img, 'security_test.png')

# 11. ADDNEW / NEW: Plus Badge
def create_addnew():
    img = draw_base_badge((37, 99, 235), (59, 130, 246))
    draw = ImageDraw.Draw(img)
    pw, pl = 30, 85
    draw.line([(CX - pl, CY), (CX + pl, CY)], fill=(255, 255, 255, 255), width=pw, joint='curve')
    draw.line([(CX, CY - pl), (CX, CY + pl)], fill=(255, 255, 255, 255), width=pw, joint='curve')
    save_icon(img, 'addnew.png')
    save_icon(img, 'new.png')

# 12. VIEW: Modern Eye
def create_view():
    img = draw_base_badge((99, 102, 241), (129, 140, 248))
    draw = ImageDraw.Draw(img)
    # Eye contour
    draw.arc([CX - 115, CY - 75, CX + 115, CY + 75], start=0, end=180, fill=(255, 255, 255, 255), width=18)
    draw.arc([CX - 115, CY - 75, CX + 115, CY + 75], start=180, end=360, fill=(255, 255, 255, 255), width=18)
    # Pupil
    draw.ellipse([CX - 35, CY - 35, CX + 35, CY + 35], fill=(255, 255, 255, 255))
    save_icon(img, 'view.png')

# 13. DOC: Document
def create_doc():
    img = draw_base_badge((225, 29, 72), (244, 63, 94))
    draw = ImageDraw.Draw(img)
    dw, dh = 160, 210
    d_left = CX - dw // 2
    d_top = CY - dh // 2
    draw.rounded_rectangle([d_left, d_top, d_left + dw, d_top + dh], radius=20, outline=(255, 255, 255, 255), width=16)
    draw.line([(d_left + 35, CY - 30), (d_left + dw - 35, CY - 30)], fill=(255, 255, 255, 255), width=12)
    draw.line([(d_left + 35, CY + 10), (d_left + dw - 35, CY + 10)], fill=(255, 255, 255, 255), width=12)
    draw.line([(d_left + 35, CY + 50), (d_left + dw - 70, CY + 50)], fill=(255, 255, 255, 255), width=12)
    save_icon(img, 'doc.png')

# Run all generators
if __name__ == '__main__':
    create_development()
    create_internaltest()
    create_uat()
    create_deployment()
    create_piloting()
    create_production()
    create_onhold()
    create_reject()
    create_requirement()
    create_security_test()
    create_addnew()
    create_view()
    create_doc()
    print("All modern icons generated successfully!")
