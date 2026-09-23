"""Compare browser output against the original Figma crop; create QA artifacts."""

import json
from pathlib import Path

from PIL import Image, ImageChops, ImageEnhance, ImageStat

root = Path(__file__).resolve().parent.parent / "screenshots"
reference = Image.open(root / "reference_full.png").convert("RGB").crop((0, 800, 1440, 2420))
actual = Image.open(root / "browser_1440.png").convert("RGB")
reference.save(root / "reference_1440.png")
if actual.size != reference.size:
  raise SystemExit(f"Image sizes differ: Figma {reference.size}, browser {actual.size}")

Image.blend(reference, actual, .5).save(root / "overlay_50.png")
difference = ImageChops.difference(reference, actual)
ImageEnhance.Contrast(difference).enhance(4).save(root / "difference_4x.png")
regions = {
  "all": (0, 0, 1440, 1620),
  "tabs_and_toolbar": (100, 0, 1340, 144),
  "reviews": (100, 160, 1340, 926),
  "load_more": (605, 962, 835, 1014),
  "heading": (400, 1078, 1040, 1136),
  "product_images": (100, 1191, 1340, 1489),
  "product_text": (100, 1505, 1340, 1599),
}
results = {}
for name, box in regions.items():
  delta = ImageChops.difference(reference.crop(box), actual.crop(box))
  red, green, blue = delta.split()
  maximum = ImageChops.lighter(ImageChops.lighter(red, green), blue)
  histogram = maximum.histogram()
  count = sum(histogram)
  results[name] = {
    "mean_absolute_channel_error_0_255": round(sum(ImageStat.Stat(delta).mean) / 3, 4),
    "pixels_with_any_difference_percent": round(sum(histogram[1:]) / count * 100, 4),
    "pixels_with_difference_over_16_percent": round(sum(histogram[17:]) / count * 100, 4),
  }
(root / "comparison.json").write_text(json.dumps(results, indent=2) + "\n", encoding="utf-8")
print(json.dumps(results, indent=2))
