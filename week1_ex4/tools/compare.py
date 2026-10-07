"""Compare the complete desktop render against the exported Figma frame."""

import json
from pathlib import Path

from PIL import Image, ImageChops, ImageEnhance, ImageStat

root = Path(__file__).resolve().parent.parent / "screenshots"
reference_path = root.parent.parent / "design-references/figma/9KY9hfTbbqUj3Cz8Mo19EG/screens/product-detail-desktop-1-2.png"
reference = Image.open(reference_path).convert("RGB")
actual = Image.open(root / "browser_1440.png").convert("RGB")
if actual.size != reference.size:
  raise SystemExit(f"Image sizes differ: Figma {reference.size}, browser {actual.size}")

Image.blend(reference, actual, .5).save(root / "overlay_50.png")
difference = ImageChops.difference(reference, actual)
ImageEnhance.Contrast(difference).enhance(4).save(root / "difference_4x.png")
regions = {
  "all": (0, 0, 1440, 3066),
  "header": (0, 0, 1440, 112),
  "product_overview": (0, 112, 1440, 800),
  "tabs_and_toolbar": (100, 800, 1340, 944),
  "reviews": (100, 960, 1340, 1726),
  "load_more": (605, 1762, 835, 1814),
  "related_heading": (400, 1878, 1040, 1936),
  "product_images": (100, 1991, 1340, 2289),
  "product_text": (100, 2305, 1340, 2399),
  "newsletter": (100, 2476, 1340, 2656),
  "footer": (0, 2566, 1440, 3066),
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

mobile_reference_path = root.parent.parent / "design-references/figma/9KY9hfTbbqUj3Cz8Mo19EG/screens/product-detail-mobile-35-1062.png"
mobile_reference = Image.open(mobile_reference_path).convert("RGB")
mobile_actual = Image.open(root / "browser_390.png").convert("RGB")
if mobile_actual.size != mobile_reference.size:
  raise SystemExit(f"Mobile image sizes differ: Figma {mobile_reference.size}, browser {mobile_actual.size}")
Image.blend(mobile_reference, mobile_actual, .5).save(root / "overlay_mobile_50.jpg", quality=88)
mobile_difference = ImageChops.difference(mobile_reference, mobile_actual)
ImageEnhance.Contrast(mobile_difference).enhance(4).save(root / "difference_mobile_4x.png")
mobile_regions = {
  "all": (0, 0, 390, 3553),
  "promo_header_and_breadcrumb": (0, 0, 390, 156),
  "gallery": (0, 156, 390, 568),
  "product_summary": (0, 568, 390, 1094),
  "tabs_and_toolbar": (0, 1094, 390, 1256),
  "reviews": (0, 1256, 390, 2044),
  "related_products": (0, 2044, 390, 2540),
  "newsletter": (0, 2540, 390, 2870),
  "footer": (0, 2707, 390, 3553),
}
mobile_results = {}
for name, box in mobile_regions.items():
  delta = ImageChops.difference(mobile_reference.crop(box), mobile_actual.crop(box))
  red, green, blue = delta.split()
  maximum = ImageChops.lighter(ImageChops.lighter(red, green), blue)
  histogram = maximum.histogram()
  count = sum(histogram)
  mobile_results[name] = {
    "mean_absolute_channel_error_0_255": round(sum(ImageStat.Stat(delta).mean) / 3, 4),
    "pixels_with_any_difference_percent": round(sum(histogram[1:]) / count * 100, 4),
    "pixels_with_difference_over_16_percent": round(sum(histogram[17:]) / count * 100, 4),
  }
mobile_report = {
  "reference": "../design-references/figma/9KY9hfTbbqUj3Cz8Mo19EG/screens/product-detail-mobile-35-1062.png",
  "reference_encoding": "PNG screenshot export from editable Figma node 35:1062",
  "regions": mobile_results,
}
(root / "comparison_mobile.json").write_text(json.dumps(mobile_report, indent=2) + "\n", encoding="utf-8")
print(json.dumps({"desktop": results, "mobile": mobile_report}, indent=2))
