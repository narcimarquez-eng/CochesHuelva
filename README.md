# CochesHuelva
Carrera de coches por Huelva

## Prototipo

`carreras_huelva.html` es un juego de conducción en 3D que corre en el navegador. Usa Three.js y JSZip desde cdnjs, así que necesita conexión.

1. Abre `carreras_huelva.html` en el navegador.
2. Carga `zona-huelva.zip` con el selector de archivo. Es la zona exportada por «Paseo por el relieve».
3. Pulsa **Nueva carrera**: 3 vueltas a un circuito generado sobre las calles de la zona.

Controles: flechas o WASD, o los botones de la pantalla en el móvil. `M` abre el menú. Los límites de velocidad son solo informativos.

## Datos

`zona-huelva.zip` incluye datos de OpenStreetMap (© colaboradores de OpenStreetMap, licencia ODbL) y del Catastro (Dirección General del Catastro). El juego solo usa `calles.geojson` y `casas.geojson`.
