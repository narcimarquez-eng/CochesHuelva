# CochesHuelva
Carrera de coches por Huelva

## Prototipo

`carreras_huelva.html` es un juego de conducción en 3D que corre en el navegador. Usa Three.js y JSZip desde cdnjs, así que necesita conexión. Las fachadas se dibujan con ventanas, puertas y zócalo a partir de los colores del Catastro; las casas sin foto usan colores de una paleta.

1. Abre `carreras_huelva.html` en el navegador.
2. La zona se carga sola desde `zona-huelva.zip`, que va junto al juego (es la zona exportada por «Paseo por el relieve»). Si abres el HTML desde el disco y no la encuentra, elige el zip con el selector.
3. Pulsa **Nueva carrera**: 3 vueltas a un circuito generado sobre las calles de la zona.

En la carrera hay tráfico: coches que circulan por la derecha de las calles y que frenan al jugador como un muro, y peatones por las aceras. La ruta se marca con flechas amarillas pintadas en la calzada, solo el tramo hasta el siguiente checkpoint.

El suelo es la ortofoto aérea de la zona (la del paseo), con sus calles, campos y ríos. Encima se dibujan las calles con asfalto, aceras de losetas, bordillos, marcas viales, pasos de cebra, farolas y árboles en las avenidas, árboles en parques y praderas, agua (ríos, lagos, humedales y piscinas), quitamiedos en las curvas de las vías rápidas y rotondas con isleta. Si el coche sube a la acera da un bote, va más despacio y el volante responde menos.

Controles: flechas o WASD. `C` (o el botón **Cámara**) cambia entre la vista de detrás del coche, la de cerca y la de dentro. En el móvil, la ruleta de la pantalla gira el coche y los botones ▲ (acelerar) y ■ (frenar) están abajo. `M` abre el menú. Los límites de velocidad son solo informativos.

El coche tiene caja automática de seis marchas. Arriba a la izquierda se ven la marcha y el tacómetro: la caja sube hacia las 6200 rpm y baja al acelerar a fondo con pocas vueltas o al frenar hasta casi parar. Para ir marcha atrás, para del todo y pisa el freno medio segundo; otro frenazo parado vuelve a marcha adelante. La física es la de un turismo de unos 150 CV: de 0 a 100 km/h en unos 8 s, frenada de 100 a 0 en unos 45 m y punta de unos 200 km/h. El volante responde menos a más velocidad, y en las curvas rápidas el coche pierde velocidad si el asfalto no aguanta el giro.

## Instalar en el móvil

Activa GitHub Pages en Settings → Pages, eligiendo la rama del juego y la carpeta raíz. Después abre `https://narcimarquez-eng.github.io/CochesHuelva/carreras_huelva.html` en Chrome y elige la opción de añadirlo a la pantalla de inicio. Hay que abrirlo desde esa URL: desde un archivo local no se puede instalar. El icono está en `icon.svg` y `icon-512.png`.

## Coches

En la pantalla de inicio se elige el coche: el básico (una caja roja), uno de la galería, CarConcept o ToyCar.

La galería son ocho turismos de Kenney Car Kit, © Kenney (kenney.nl), licencia CC0 1.0: Berlina, Hatchback, Deportivo, SUV, SUV de lujo, Taxi, Patrulla y Furgoneta. Están en `modelos/coches/`, con sus datos en `catalogo.json`. Miden 4,2 m de largo y el frontal mira a +Z, que es el sentido en el que avanza el juego.

CarConcept y ToyCar vienen de los modelos de ejemplo de Khronos glTF y están optimizados:

- CarConcept, © Eric Chadwick (Darmstadt Graphics Group), licencia CC BY 4.0.
- ToyCar, © Guido Odendahl y Eric Chadwick, licencia CC0.

En el tráfico de la carrera circulan los coches de la galería y versiones ligeras de CarConcept y ToyCar (`modelos/trafico-*.glb`, con un 10 % de los triángulos), hechas con Blender a partir de los originales.

`vendor/GLTFLoader.js` es el cargador de GLTF de three.js r128 (licencia MIT).

## Datos

`zona-huelva.zip` incluye datos de OpenStreetMap (© colaboradores de OpenStreetMap, licencia ODbL) y del Catastro (Dirección General del Catastro). El juego usa `calles.geojson` y `casas.geojson`, `mapa.geojson` (usos del suelo: parques, praderas, cultivos, agua), `datos/osm.json` (piscinas y rotondas) y `ortofoto.jpg` con `ortofoto.jgw` (foto aérea del suelo). Si faltan, la zona carga igual, sin ese paisaje.

La ortofoto es PNOA, © Instituto Geográfico Nacional de España, licencia CC BY 4.0.
