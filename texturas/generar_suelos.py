# Texturas de detalle del suelo (césped, arena y tierra) para carreras_huelva.html.
# Son de «relieve»: su media es gris medio (128) en cada canal, y el juego multiplica por ellas el color de la foto aérea,
# así que no cambian el color del suelo, solo le añaden las briznas, los granos y las piedras que la foto no ve.
# Se repiten sin costuras (todo se hace sobre un toro). Uso: python3 texturas/generar_suelos.py
import os
import numpy as np
from PIL import Image

N = 512
DIR = os.path.dirname(os.path.abspath(__file__))


def ruido(rng, escala):
    """Ruido periódico: ruido blanco suavizado (manchas de unos «escala» píxeles), con media 0 y desviación 1."""
    f = np.fft.fftfreq(N)[:, None] ** 2 + np.fft.fftfreq(N)[None, :] ** 2
    filtro = np.exp(-f * (escala ** 2) * 2.0)
    r = np.real(np.fft.ifft2(np.fft.fft2(rng.standard_normal((N, N))) * filtro))
    return (r - r.mean()) / (r.std() + 1e-9)


def trazo(img, x0, y0, ang, largo, ancho, valor, alfa):
    """Brizna o grieta: un trazo recto que da la vuelta por los bordes (la textura se repite)."""
    pasos = max(2, int(largo * 2))
    for k in range(pasos):
        t = k / (pasos - 1)
        x, y = x0 + np.cos(ang) * largo * t, y0 + np.sin(ang) * largo * t
        a = ancho * (1 - 0.7 * t)   # más fina en la punta
        r = max(1, int(np.ceil(a)))
        for dy in range(-r, r + 1):
            for dx in range(-r, r + 1):
                d = np.hypot(dx, dy)
                if d > a:
                    continue
                i, j = int(y + dy) % N, int(x + dx) % N
                img[i, j] = img[i, j] * (1 - alfa) + np.asarray(valor) * alfa


def mancha(img, x0, y0, rx, ry, ang, valor, sombra):
    """Piedra o concha: elipse con su sombra abajo a la derecha y un brillo arriba a la izquierda."""
    r = int(np.ceil(max(rx, ry))) + 2
    c, s = np.cos(ang), np.sin(ang)
    for dy in range(-r, r + 1):
        for dx in range(-r, r + 1):
            u, v = (dx * c + dy * s) / rx, (-dx * s + dy * c) / ry
            i, j = int(y0 + dy) % N, int(x0 + dx) % N
            d = u * u + v * v
            if d <= 1:
                luz = 1 + 0.18 * (-(dx + dy) / (rx + ry))   # brillo hacia la luz
                img[i, j] = np.asarray(valor) * luz
            elif d <= 1.6 and dx + dy > 0:
                img[i, j] = img[i, j] * (1 - sombra)


def normaliza(img, contraste, color=0.35):
    """Media de cada canal a 128 y la desviación que se pida; del color, solo una parte (sobre todo es luz y sombra)."""
    out = np.empty_like(img)
    for k in range(3):
        canal = img[..., k]
        canal = (canal - canal.mean()) / (canal.std() + 1e-9)
        out[..., k] = canal
    lum = out.mean(axis=2, keepdims=True)
    out = lum + (out - lum) * color
    return np.clip(128 + out * contraste, 0, 255).astype(np.uint8)


def cesped(rng):
    img = np.ones((N, N, 3)) * 0.5
    matas = ruido(rng, 40)   # matas más densas y huecos
    img *= (1 + 0.25 * matas)[..., None]
    for _ in range(26000):
        x, y = rng.uniform(0, N, 2)
        ang = rng.uniform(0, 2 * np.pi)
        tono = rng.choice([0, 1, 2], p=[0.55, 0.3, 0.15])
        valor = [(0.42, 0.58, 0.30), (0.62, 0.66, 0.36), (0.26, 0.38, 0.20)][tono]
        trazo(img, x, y, ang, rng.uniform(5, 14), rng.uniform(0.6, 1.2), valor, 0.85)
    img *= (1 + 0.12 * ruido(rng, 3))[..., None]
    return normaliza(img, 40)


def arena(rng):
    yy, xx = np.mgrid[0:N, 0:N] / N
    # ondas del viento: bandas con la fase movida por un ruido suave
    fase = ruido(rng, 90) * 0.9
    ondas = np.sin(2 * np.pi * (xx * 9 + yy * 3) + fase * 2.5)
    ondas = np.sign(ondas) * np.abs(ondas) ** 0.6
    grano = ruido(rng, 1.2)
    img = np.ones((N, N, 3)) * 0.75
    img *= (1 + 0.11 * ondas * (0.55 + 0.45 * ruido(rng, 60)) + 0.07 * ruido(rng, 25) + 0.09 * grano)[..., None]
    for _ in range(260):   # conchas y piedrecitas
        x, y = rng.uniform(0, N, 2)
        r = rng.uniform(1.0, 2.6)
        v = rng.choice([0.55, 0.95]) * np.array([1.0, 0.97, 0.9])
        mancha(img, x, y, r, r * rng.uniform(0.6, 1), rng.uniform(0, np.pi), v * 0.75, 0.15)
    return normaliza(img, 17, 0.2)


def tierra(rng):
    img = np.ones((N, N, 3)) * 0.5
    img *= (1 + 0.22 * ruido(rng, 30) + 0.12 * ruido(rng, 6) + 0.08 * ruido(rng, 1.5))[..., None]
    # grietas: bordes de celdas de Voronoi (periódicas)
    pts = rng.uniform(0, N, (60, 2))
    yy, xx = np.mgrid[0:N, 0:N]
    d1 = np.full((N, N), 1e9)
    d2 = np.full((N, N), 1e9)
    for px, py in pts:
        for ox in (-N, 0, N):
            for oy in (-N, 0, N):
                d = np.hypot(xx - px - ox, yy - py - oy)
                d2 = np.minimum(d2, np.maximum(d1, d))
                d1 = np.minimum(d1, d)
    grieta = np.clip(1 - (d2 - d1) / 2.2, 0, 1) * (ruido(rng, 20) > -0.2)
    img *= (1 - 0.35 * grieta)[..., None]
    for _ in range(900):   # piedras
        x, y = rng.uniform(0, N, 2)
        r = rng.uniform(1.2, 4.5)
        v = rng.uniform(0.45, 0.75) * np.array([1.0, 0.95, 0.88])
        mancha(img, x, y, r, r * rng.uniform(0.5, 0.9), rng.uniform(0, np.pi), v, 0.3)
    for _ in range(2200):   # hierba seca en matas sueltas
        cx, cy = rng.uniform(0, N, 2)
        for _ in range(rng.integers(3, 9)):
            trazo(img, cx + rng.normal(0, 3), cy + rng.normal(0, 3), rng.uniform(0, 2 * np.pi), rng.uniform(4, 10), 0.7,
                  (0.72, 0.64, 0.40), 0.8)
    return normaliza(img, 26)


if __name__ == '__main__':
    for nombre, fn, semilla in (('cesped', cesped, 11), ('arena', arena, 22), ('tierra', tierra, 33)):
        Image.fromarray(fn(np.random.default_rng(semilla))).save(os.path.join(DIR, nombre + '.jpg'), quality=86)
        print(nombre + '.jpg')
