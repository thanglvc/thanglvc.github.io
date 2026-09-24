import json
from pathlib import Path
from PIL import Image, ImageChops, ImageEnhance, ImageStat

root = Path(__file__).resolve().parent.parent / "screenshots"
reference_path = root / "reference_1300x2216.png"
actual_path = root / "screenshot_desktop_1300.png"

if not reference_path.exists() or not actual_path.exists():
    raise SystemExit(f"Missing images: ref {reference_path.exists()}, actual {actual_path.exists()}")

ref = Image.open(reference_path).convert("RGB")
act = Image.open(actual_path).convert("RGB")

print(f"Ref size: {ref.size}, Act size: {act.size}")

max_w = max(ref.width, act.width)
max_h = max(ref.height, act.height)

ref_canvas = Image.new("RGB", (max_w, max_h), (255, 255, 255))
ref_canvas.paste(ref, (0, 0))

act_canvas = Image.new("RGB", (max_w, max_h), (255, 255, 255))
act_canvas.paste(act, (0, 0))

# 50% overlay
overlay = Image.blend(ref_canvas, act_canvas, 0.5)
overlay.save(root / "overlay_50.png")

# Difference image
diff = ImageChops.difference(ref_canvas, act_canvas)
diff.save(root / "diff_content.png")
ImageEnhance.Contrast(diff).enhance(4).save(root / "diff_4x.png")

# Blink GIF
ref_canvas.save(
    root / "blink_comparison.gif",
    save_all=True,
    append_images=[act_canvas],
    duration=500,
    loop=0
)

delta = ImageChops.difference(ref_canvas, act_canvas)
red, green, blue = delta.split()
maximum = ImageChops.lighter(ImageChops.lighter(red, green), blue)
histogram = maximum.histogram()
total_pixels = sum(histogram)

stat = ImageStat.Stat(delta)
results = {
    "reference_size": list(ref.size),
    "actual_size": list(act.size),
    "mean_absolute_channel_error_0_255": round(sum(stat.mean) / 3, 4),
    "pixels_with_any_difference_percent": round(sum(histogram[1:]) / total_pixels * 100, 4),
    "pixels_with_difference_over_16_percent": round(sum(histogram[17:]) / total_pixels * 100, 4)
}

(root / "comparison.json").write_text(json.dumps(results, indent=2) + "\n", encoding="utf-8")
print(json.dumps(results, indent=2))
