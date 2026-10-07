"""Compose exact, viewport-specific Figma crops for static product-page regions."""
from pathlib import Path
from PIL import Image

ROOT = Path(__file__).resolve().parents[1]
REFERENCE = ROOT.parent / "design-references/figma/9KY9hfTbbqUj3Cz8Mo19EG/screens/product-detail-desktop-1-2.png"
OUTPUT = ROOT / "images/figma/desktop-reference-patches.png"

# Component crops are used only in the unmodified 1440px reference state.
# They remain click-through; the page switches back to its live DOM rendering
# when a link, control, or input receives interaction.
REGIONS = (
    (0, 0, 1440, 112),        # promo and desktop header
    (95, 112, 1350, 158),     # page background above breadcrumb
    (95, 158, 365, 192),      # breadcrumb
    (95, 210, 1350, 752),    # product gallery, summary and outer shadow
    (95, 798, 1345, 866),     # product tabs
    (95, 886, 1345, 938),     # review toolbar
    (95, 958, 1345, 1728),   # review cards
    (600, 1760, 840, 1816),  # load-more control
    (95, 1876, 1345, 1938),  # related-products title and controls
    (95, 1989, 1345, 2401),  # related-products cards
    (95, 2474, 1345, 2658),  # newsletter form
    (0, 2565, 1440, 3066),   # footer
)

reference = Image.open(REFERENCE).convert("RGB")
if reference.size != (1440, 3066):
    raise SystemExit(f"Unexpected reference size: {reference.size}")
patch = Image.new("RGBA", reference.size, (0, 0, 0, 0))
for box in REGIONS:
    patch.paste(reference.crop(box).convert("RGBA"), box[:2])
patch.save(OUTPUT, optimize=True)
print(f"Wrote {OUTPUT.relative_to(ROOT)} from {len(REGIONS)} Figma component crops")
