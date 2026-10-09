# CochesHuelva
Carrera de coches por Huelva

## Prototipo

`carreras_huelva.html` es un juego de conducción en 3D que corre en el navegador. Usa Three.js y JSZip desde cdnjs, así que necesita conexión. Las fachadas se dibujan con ventanas, puertas y zócalo a partir de los colores del Catastro; las casas sin foto usan colores de una paleta.

1. Abre `carreras_huelva.html` en el navegador.
2. La zona se carga sola desde `zona-huelva.zip`, que va junto al juego (es la zona exportada por «Paseo por el relieve»). Si abres el HTML desde el disco y no la encuentra, elige el zip con el selector.
3. Elige el modo y la hora del día y pulsa **Nueva partida**.

## Modos de juego

- **Circuito · 3 vueltas**: un circuito generado sobre las calles de la zona, con tres checkpoints y la meta. Tiene decoración de carrera: un arco de salida y meta con un semáforo de cinco luces que se encienden en la cuenta atrás y se ponen verdes al «¡YA!», línea a cuadros, pancartas y público que salta cuando pasas, arcos hinchables de colores en cada checkpoint y unos **boxes** a 40 m de la salida. Al cruzar la meta en la última vuelta hay fuegos artificiales y aplausos, y el resumen dice los tiempos y cómo ha quedado el coche.
- **Exploración**: una ruta por la ciudad de destino en destino, sin vueltas. Cada destino está a unos 350–900 m, lejos de los ya visitados y por calles que aún no has recorrido; arriba a la derecha se ven la calle del destino, la distancia, las paradas y los kilómetros. Es infinita: se termina desde el menú.

La ruta se marca con flechas y una línea **verdes** en la calzada, solo las del tramo actual y desde el coche en adelante. Si te sales del camino, a los pocos segundos se calcula cómo volver desde donde estás y las flechas y la línea se ponen **rojas**, con un aviso; al volver a la ruta vuelven a ser verdes.

## Daño del coche

Cada golpe suma daño según la velocidad y lo que golpeas (una casa o un árbol más que un quitamiedos o una pancarta) y se reparte por zonas: delante, detrás, izquierda y derecha. El indicador del HUD pinta cada zona de verde a rojo y da el total.

- El daño delantero le quita potencia al motor; el total baja la velocidad punta (con mucho daño no pasa de unos 90 km/h); el trasero quita agarre, y un lateral tocado hace que el volante tire hacia ese lado.
- La chapa se abolla de verdad donde golpeas: la carrocería del modelo se hunde.
- Con más del 35 % el motor echa humo (negro a partir del 55 %), falla y traquetea; por encima del 85 % salen llamas. Las luces de freno y los faros se rompen con los golpes de atrás o de delante.
- Al 100 % el coche queda destrozado: se para y, en el menú, puedes llamar a la grúa (en el circuito cuesta 30 s) o empezar otra partida.
- Los **talleres** (un aro azul con una llave) lo arreglan: entra despacio o párate dentro. En el circuito están los boxes; en la exploración siempre hay tres talleres a menos de 700 m, y cuando el daño pasa del 25 % el HUD dice dónde está el más cercano.

## La ciudad viva

- Peatones que pasean, que corren y que **pasean al perro**: los perros van con correa delante de su dueño, trotan, mueven el rabo y ladran si pasas cerca y deprisa.
- Peatones que **cruzan por los pasos de cebra**: esperan en el bordillo a que no venga nadie deprisa, y los coches del tráfico paran para dejarles pasar.
- **Ciclistas** por el borde derecho de la calzada: frenan detrás de los coches, te tocan el timbre si les cortas el paso, y si les das se caen (y se levantan al rato).
- El **claxon** (botón 📢 o tecla `H`) hace que los peatones de delante se vuelvan y den un respingo, que los perros ladren y que los ciclistas contesten con el timbre. Después de un golpe fuerte, la gente de alrededor se para a mirar.

## Hora del día

**Día**, **atardecer** (sol bajo y naranja, sombras largas) o **noche** (cielo con estrellas y luna). Al atardecer y de noche se encienden las ventanas de las casas, las farolas (con su charco de luz en el suelo) y los faros de todos los coches, que alumbran el asfalto por delante. La elección se recuerda.


En la partida hay tráfico: coches que circulan por la derecha de las calles, frenan si tienen a alguien delante (y pitan si el jugador les corta el paso) y encienden los pilotos al frenar. Si chocas con uno, los dos rebotan según la velocidad y el ángulo del golpe; el de tráfico sale despedido, gira, se queda parado con las luces de emergencia y luego vuelve a su carril. Los peatones van por las aceras y se apartan de un salto si el coche se les echa encima. El siguiente checkpoint (o el destino, en azul) es una columna de luz; los demás solo muestran un aro en el suelo, y arriba a la derecha una flecha indica hacia dónde queda.

El suelo es la ortofoto aérea de la zona (la del paseo), con sus calles, campos y ríos. Encima se dibujan las calles con asfalto, aceras de losetas, bordillos, rigolas, marcas viales, pasos de cebra, farolas y árboles en las avenidas, árboles en parques y praderas, agua (ríos, lagos, humedales y piscinas), quitamiedos de doble onda con postes y captafaros en las curvas de las vías rápidas y rotondas con isleta. Hay cielo con degradado y sol, y en el ordenador sombras de edificios, árboles y coches alrededor del jugador (en el móvil no, para que vaya fluido). Los edificios se dibujan por bloques de 300 m para que solo se pinten los que se ven. Las aceras y los bordillos se cortan donde entran en la calzada de otra calle (cruces, calzadas dobles), y la calzada y las aceras usan texturas de asfalto y adoquín (`texturas/`) generadas con Blender para este proyecto.

### Choques

El coche choca con tres círculos a lo largo de su eje (morro, centro y cola), así que entra por sitios estrechos y los golpes en una esquina lo hacen girar. Cada cosa responde a su manera: las casas y los árboles lo paran y rebota un poco; el quitamiedos lo desvía y lo deja seguir rozando, con chispas; el agua no deja pasar. Subir a la acera de lado y despacio da un bote y la conducción se pone dura (no pasa de 25 km/h); embestir el bordillo de frente y deprisa hace que rebote hacia la calzada. Los golpes fuertes sacuden la cámara y tiñen de rojo los bordes de la pantalla.

### Conducción y sonido

La carrocería cabecea al acelerar y frenar y se inclina en las curvas. Si el asfalto no aguanta el giro, el coche derrapa hacia fuera; al frenar fuerte, derrapar o salir a fondo, los neumáticos chirrían, echan humo y dejan marcas negras en el asfalto, y las luces de freno se encienden. Con la velocidad se abre el ángulo de la cámara.

Todo el sonido se sintetiza en el navegador (Web Audio, sin archivos): el motor sigue las vueltas y el gas (con el corte de inyección y el bajón al cambiar de marcha), la admisión, la rodadura y el aire, el chirrido de las ruedas, el roce de chapa, los golpes (metálicos contra el quitamiedos y las farolas, de madera contra los árboles, con cristales contra otro coche), los botes en el bordillo, las bocinas del tráfico, la cuenta atrás y los checkpoints. El botón 🔊 (o la tecla `N`) lo quita y lo recuerda.

### Controles

Teclado: flechas o WASD. `H` toca el claxon. `C` (o el botón **Cámara**) cambia entre la vista de detrás del coche, la de cerca y la de dentro; la de dentro va a la altura de un asiento alto para ver bien la calle. `M` (o **Menú**) pausa la partida; **Continuar** vuelve a ella.

En el móvil, abajo a la izquierda hay una **ruleta**: se gira con el dedo alrededor del centro (140° a cada lado es el giro completo), vibra a cada muesca, un arco ámbar marca cuánto giras y al soltarla vuelve sola al centro. A la derecha están los pedales **GAS** y **FRENO**, grandes y de colores, y el botón **D/R** de la marcha. Los límites de velocidad son solo informativos.

El coche tiene caja automática de seis marchas. Arriba a la izquierda se ven la marcha y el tacómetro: la caja sube hacia las 6200 rpm y baja al acelerar a fondo con pocas vueltas o al frenar hasta casi parar. Para ir **marcha atrás** hay dos maneras:

- Parado, mantén el freno (↓ o **FRENO**): a las tres décimas entra la R y el freno empuja hacia atrás (y el acelerador frena). Para volver, pisa el acelerador: frena, y parado pone la marcha adelante.
- Con el coche casi parado, toca **D/R** (o la tecla `R`): con la R así puesta, el acelerador va hacia atrás y el freno frena, como en un coche automático. Otro toque vuelve a la D.

La física es la de un turismo de unos 150 CV: de 0 a 100 km/h en unos 8 s, frenada de 100 a 0 en unos 45 m y punta de unos 200 km/h. El volante responde menos a más velocidad.

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

## Árboles y peatones

Los árboles son modelos de Kenney Nature Kit (roble, álamo, pino y arbusto), con licencia CC0: están en `modelos/arboles/`. Los peatones son ocho figuras de Kenney Blocky Characters (CC0), en `modelos/peatones/`, de 1,75 m. Las licencias de cada paquete van junto a los modelos. Se dibujan solo los árboles cercanos al coche, para que el móvil no sufra.

## Datos

`zona-huelva.zip` incluye datos de OpenStreetMap (© colaboradores de OpenStreetMap, licencia ODbL) y del Catastro (Dirección General del Catastro). El juego usa `calles.geojson` y `casas.geojson`, `mapa.geojson` (usos del suelo: parques, praderas, cultivos, agua), `datos/osm.json` (piscinas y rotondas) y `ortofoto.jpg` con `ortofoto.jgw` (foto aérea del suelo). Si faltan, la zona carga igual, sin ese paisaje.

La ortofoto es PNOA, © Instituto Geográfico Nacional de España, licencia CC BY 4.0.
