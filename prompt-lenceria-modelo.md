# PROMPT — Reemplazo de modelo / Catálogo lencería premium
## Contexto de uso
Prompt para pipeline image-to-image (seedream o equivalente).  
Objetivo: máxima coherencia de estudio entre todas las imágenes del catálogo.

---

## PROMPT PRINCIPAL

```
Intimate apparel catalog, editorial fashion photography.

GARMENT: exact color from reference (do not alter hue/saturation). Same lace pattern, corset bodice, underwire cups, garter straps, metal ring hardware, thong brief, leg bands. Zero deviation.

MODEL: Argentine woman, Italian/Spanish descent, fair porcelain skin (NOT tanned). Dark brown hair, straight or wavy. Face visible, looking to side. Photorealistic face — defined jaw, angular cheekbones, natural asymmetry, real skin texture. NOT generic AI face. No tattoos, no piercings, clean skin. Different pose from reference (contrapposto, hand on hip).

LIGHTING: natural backlight through sheer white voile curtains, left side. Background near-overexposed glowing white. No frontal light, no softbox. Soft rim light on body edges only. Warm-neutral whites 5200K. White wood floor, faint leg reflection. Ethereal high-key atmosphere.

SHOT: full body vertical, 85mm f/2.0, bokeh curtains, sharp focus on garment and face.
```

---

## PARÁMETROS TÉCNICOS SUGERIDOS

| Parámetro | Valor |
|---|---|
| Modelo | FLUX.1-dev + ControlNet / seedream-5-lite / SDXL |
| Strength (img2img) | 0.65 – 0.75 |
| CFG Scale | 7 – 8 |
| Steps | 30 – 40 |
| Aspect ratio | 2:3 (vertical) |

---

## NEGATIVE PROMPT

```
tattoos, piercings, body marks, moles, blemishes, jewelry, dark background,
colored background, studio flash, softbox lighting, frontal fill light, harsh shadows,
cold white background, grey backdrop, studio look, indoor photography without window light,
overexposed skin, different garment color, different garment style, nudity, explicit content,
low quality, blur on garment, distorted fabric,
tanned skin, bronzed skin, warm golden skin, orange skin tone,
AI face, doll face, angelic face, overly perfect symmetry, plastic skin,
airbrushed face, overly smooth skin, generic model face, unrealistic features,
closed eyes, looking down, cropped face
```

---

## NOTAS DE CONSISTENCIA

- Usar siempre la misma imagen de referencia como input
- Mantener strength entre 0.65–0.75 para preservar prenda pero cambiar modelo
- Si el modelo bloquea: reformular como *"professional fashion photography, intimate apparel catalog"*
- Seed fijo para lotes del mismo shooting: anotar seed del primer resultado aprobado

---

*Generado a partir de análisis técnico de imagen editorial — TukiPass assets*
