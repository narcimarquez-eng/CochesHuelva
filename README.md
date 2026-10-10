# CochesHuelva
Carrera de coches por Huelva

## Prototipo

`carreras_huelva.html` es un juego de conducción en 3D que corre en el navegador. Usa Three.js y JSZip desde cdnjs, así que necesita conexión. Las fachadas se dibujan con ventanas, puertas y zócalo a partir de los colores del Catastro; las casas sin foto usan colores de una paleta.

1. Abre `carreras_huelva.html` en el navegador.
2. La zona se carga sola desde `zona-huelva.zip`, que va junto al juego (es la zona exportada por «Paseo por el relieve»). Si abres el HTML desde el disco y no la encuentra, elige el zip con el selector.
3. Elige el modo y la hora del día y pulsa **Nueva partida**.

## Otros lugares

El juego trae Huelva, pero se puede correr en cualquier sitio de España con el **Paseo por el relieve** (`paseo.html`, la misma página de meteohuelva.es, copiada aquí):

1. En el menú del juego, en **Lugar**, pulsa **➕ Otro lugar**: se abre directamente el mapa del Paseo para elegir la zona, centrado en el lugar en el que estás.
2. Busca el pueblo o mueve el mapa y pulsa **🏁 Jugar aquí** (también vale una zona de **Zonas guardadas** o **Abrir archivo del visor**). La zona puede ser el recuadro de la pantalla o la forma que quieras: con **✏️ Dibujar**, toca el mapa para marcar sus esquinas (se arrastran; tocando una se quita; **Deshacer** y **Borrar** ayudan).
3. El Paseo descarga alturas, foto aérea, OpenStreetMap y casas del Catastro, monta la zona (en 3D, porque ahí mide el ancho de calles y aceras), la prepara como si fuera a descargar el .zip, la guarda en este navegador y vuelve solo al juego con ella. **Volver** regresa al juego sin elegir nada.

Abierto por su cuenta (sin venir del juego), el Paseo funciona como siempre; con una zona abierta en 3D, el botón **🏁 Jugar aquí** de arriba también la lleva al juego.

Las zonas traídas así (o abiertas con **📂 Abrir zip**) quedan en la fila **Lugar** del menú, con su nombre, y se pueden borrar con ✕. El juego recuerda el último lugar elegido. Al cambiar de lugar, el centro del mapa, el título y las pancartas de la carrera pasan a ser los de ese sitio.

Con una zona dibujada se descarga el rectángulo que la envuelve, pero solo se piden los trozos del Catastro que la tocan, y al prepararla para el juego (o en el .zip) se quita lo que queda fuera: las calles se cortan en el borde, y fuera no quedan casas, parcelas ni lugares. El polígono va en `zona.json` (`poligono`) y las zonas guardadas se ven con su forma en el mapa.

El Paseo y el juego tienen que estar en la misma web (por ejemplo, los dos en GitHub Pages) para compartir las zonas del navegador. El Catastro se pide a través de `catastro.php`, que está en meteohuelva.es porque GitHub Pages no ejecuta PHP.

### catastro.php nuevo

En `servidor/catastro.php` está la versión nueva, para subirla a meteohuelva.es en lugar de la actual (en la misma carpeta que `paseo.html`). Hace lo mismo que la de ahora, con las mismas respuestas y errores, y además:

- `t=parcela`: las **parcelas** del Catastro (INSPIRE `CP.CadastralParcel`), para que las vallas de los chalets vayan por su linde.
- Un recuadro sin nada (el mar, el campo) devuelve una lista vacía, en vez del error «No records» del Catastro, que el Paseo contaba como trozo fallido.
- Guarda unos días lo que llega del Catastro en una carpeta temporal del servidor: lo repetido no se vuelve a pedir.
- Deja que la llamen `narcimarquez-eng.github.io` y meteohuelva.es (lista `$ORIGENES` al principio del archivo).

Está probada contra un Catastro de mentira (mismas URL y respuestas), no contra el de verdad, que no se alcanza desde aquí. Con la versión antigua todo sigue funcionando, solo que sin parcelas: el Paseo y el juego lo detectan y lo dicen.

Sin la línea de CORS (`Access-Control-Allow-Origin`), el Paseo en GitHub Pages saca las casas de OpenStreetMap (menos y sin colores). Las zonas de más de 25 km² también salen sin el Catastro, y las muy grandes pueden ir lentas en el móvil (el menú avisa).

### Lo que falta, desde el juego

Al abrir un lugar, el menú dice si le falta algo del Catastro y deja descargarlo sin pasar por el Paseo:

- **Trozos de casas** que no llegaron cuando se preparó la zona.
- **Parcelas** (con el `catastro.php` nuevo).

**⬇️ Descargar lo que falta** los pide, los mete en la zona, la guarda en este navegador y la vuelve a abrir. Huelva también: su versión completada se guarda y se usa en lugar de la incluida.

Los **colores de las fachadas** se piden solos **mientras conduces**: la casa del Catastro sin color más cercana (a menos de 90 m), de una en una o de dos en dos. Su foto de fachada se analiza igual que en el Paseo y la casa se repinta al momento. Los colores se guardan en este navegador por referencia catastral, así que la próxima vez ya están, en esa zona o en otra que tenga las mismas casas. Si `catastro.php` no contesta, deja de pedir.

### Si no llegan las casas del Catastro

La zona se juega igual: el `casas.geojson` ya no es obligatorio y la **foto aérea se mantiene** como suelo. Si el Catastro no ha dado casas y OpenStreetMap apenas trae (menos de 0,6 por calle de vivienda), el juego **simula casas** junto a las calles para que el pueblo no salga vacío:

- **Adosadas** en fila al borde de la acera, de una o dos plantas, en el casco urbano.
- **Chalets** con su parcela y su valla donde la foto se ve con jardines (o, sin foto, fuera del casco).
- Nunca encima de otra calle, del agua, de un parque, de un campo verde de la foto ni de una casa que sí venga en los datos; las carreteras fuera del pueblo se quedan sin casas.

El menú avisa de que son simuladas y de si hay foto aérea. Sin foto, el suelo sale del mapa y los jardines de los chalets llevan césped. Si lo que falta son las calles (OpenStreetMap no respondió), el juego lo dice y pide volver a preparar la zona en el Paseo.

## Modos de juego

- **Circuito · 3 vueltas**: un circuito generado sobre las calles de la zona, con tres checkpoints y la meta, **contra cinco rivales**. Tiene decoración de carrera: un arco de salida y meta con un semáforo de cinco luces que se encienden en la cuenta atrás y se ponen verdes al «¡YA!», línea a cuadros, pancartas y público que salta cuando pasas, arcos hinchables de colores en cada checkpoint y unos **boxes** a 40 m de la salida. Al cruzar la meta en la última vuelta hay fuegos artificiales y aplausos, y el resumen dice los tiempos y cómo ha quedado el coche.
  Los rivales (pilotos inventados, con su nombre flotando encima del coche) salen en parrilla de dos en dos; tú sales el último. Siguen la vuelta frenando antes de cada curva según lo cerrada que sea, intentan adelantar al tráfico cambiando de lado y se les puede empujar: pierden velocidad, se salen de su trazada y vuelven a ella. Para que la carrera esté reñida, el que va muy por delante afloja un poco y el que va muy por detrás aprieta. Arriba a la derecha se ve tu puesto (por ejemplo, 3º/6) y a cuántos metros tienes al de delante, y se oye el motor del rival más cercano.

  Al cruzar la meta en la última vuelta hay **podio**: tres escalones de oro, plata y bronce con los coches de los tres primeros encima, fuegos artificiales, confeti y aplausos, y la tabla con el puesto, el tiempo y la diferencia de cada uno. A los rivales que aún no han llegado se les estima el tiempo con lo que les falta a su velocidad media (van con *).
- **Exploración**: una ruta por la ciudad de destino en destino, sin vueltas. Cada destino está a unos 350–900 m, lejos de los ya visitados y por calles que aún no has recorrido; arriba a la derecha se ven la calle del destino, la distancia, las paradas y los kilómetros. Es infinita: se termina desde el menú.

La ruta se marca con flechas y una línea **verdes** en la calzada, solo las del tramo actual y desde el coche en adelante. Si te sales del camino, a los pocos segundos se calcula cómo volver desde donde estás y las flechas y la línea se ponen **rojas**, con un aviso; al volver a la ruta vuelven a ser verdes.

## Casas como en el Paseo

Las casas se construyen igual que en el Paseo por el relieve:

- **Teja árabe**: tejado a dos aguas en la crujía de la fachada principal (toda la casa si tiene menos de 11 m de fondo; si no, 6,2 m y el resto plano). Lleva alero en la fachada, y atrás si la casa es poco honda, y las hileras de tejas dibujadas.
- **Azotea**: pretil de casi un metro con albardilla y, en algunas casas medianas, el torreón de la escalera.
- **Patios**: los de dentro de la casa (huecos en el Catastro) tienen sus paredes con ventanas y no los tapa el tejado.
- **Tejado de la foto**: el color de cada tejado se saca de la foto aérea, y la foto se ve encima con las tejas. Si los datos no dicen si es de teja o azotea, lo dice la foto: rojizo y de brillo medio es teja.

Las fachadas tienen ventanas con persianas y rejas, balcones, puertas, zócalo y recercados de albero. Sus colores salen de la foto de fachada del Catastro, analizada en el Paseo.

### Colores de la foto de fachada

El análisis de la foto (en el Paseo) agrupa los píxeles por **tono**, la proporción entre rojo, verde y azul, que casi no cambia entre la parte de la pared al sol y la parte a la sombra:

- El brillo cuenta poco, y el color final es el tono medio de la pared con el brillo de su parte al sol. Así un ladrillo a la sombra sigue siendo ladrillo, y no gris.
- La pared blanca a la sombra, que sale azulada, cuenta como blanca.
- Lo gris pesa menos que lo que tiene color, porque gris son también la calle, los cristales y el cielo con calima. También cuentan menos lo muy oscuro, la parte de abajo de la foto (calle y coches) y los lados (la casa de la parcela suele estar en el centro).
- Las hojas al sol, de verde amarillento, no cuentan como pared.

Antes solo se miraban los píxeles claros, así que el ladrillo y las paredes rojas en sombra quedaban fuera y salía blanco o gris. En las 157 fotos de la zona incluida se ha comprobado a ojo, y sus colores ya están rehechos. En el Paseo, los colores guardados con el análisis anterior se rehacen solos desde la foto guardada, sin descargar nada.

## Casas que cortan calles

Algunas casas del Catastro caen encima de una calzada: son sobre todo pasajes bajo edificios (calles que en OpenStreetMap pasan por debajo de una casa) o datos que no casan. En el juego el coche iría contra una pared en mitad de la calle, así que al cargar la zona se comprueba el eje de cada calle y dos líneas a un cuarto de su ancho, cada metro y medio: si una casa tiene dos o más puntos de calzada dentro, no se pone. El menú avisa de cuántas se han quitado y en qué calles (la lista entera sale al pasar el ratón por el aviso y en la consola). En la zona incluida son 100 de 32 382.

## Chalets y sus vallas

Los datos no dicen qué casa es un chalet ni dónde acaba su parcela, así que se deduce. Un chalet es una casa (o un grupo de piezas que se tocan: casa, porche, cochera):

- suelta, sin otra casa a menos de 3 m;
- baja, de dos plantas como mucho;
- de tamaño de vivienda (45–480 m² de planta);
- con sitio alrededor, sin manzana de casas a 7 m;
- a menos de 35 m de una calle.

En OpenStreetMap, además, tiene que ser una casa (`house`, `detached`, `villa`…). En la zona incluida salen unos 280.

Alrededor de cada chalet se pone la **valla** de su parcela:

- Se toma el rectángulo que lo envuelve y se abre de 2,5 a 5,5 m por cada lado. El lado que da a una calle (a menos de 12 m) llega **hasta la acera**, como casi todos los chalets.
  Con las parcelas del Catastro (`parcelas.geojson`, del Paseo o descargadas en el juego), la valla va por la **linde de la parcela** del chalet, 15 cm por dentro, y la cancela en el lado más cercano a la calle.
- Cada lado se acerca a la casa hasta que no pisa la calzada ni la acera, una piscina o el agua, ni la casa de al lado.
- Donde ya hay la valla de un vecino no se pone otra: la suya hace de medianera.
- En el lado más cercano a la calle va una **cancela** de hierro de 3 m entre dos pilares.

Hay tres tipos: murete con **reja** de barrotes, murete con **seto** recortado y **muro** alto. Los muretes son blancos, crema, albero o ladrillo, con albardilla y pilares. Las vallas chocan como un muro bajo (el coche se abolla) y ningún árbol queda en mitad de una.

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

## Mobiliario urbano y fuentes

- **Farolas** (unas 11 000) de dos estilos: báculos con brazo en las calles principales y las avenidas (en estas, entre los árboles y a los dos lados) y faroles fernandinos de hierro negro en las calles residenciales y alrededor de las rotondas. Van en las calles con acera y también, pegadas al borde, en las residenciales sin acera; en las calles anchas, a los dos lados. De noche se enciende el farol y alumbra el suelo con un charco de luz amplio. Para que el móvil aguante, se dibujan por bloques de 200 m y solo las cercanas (420 m en el móvil, 650 m en el ordenador).
- **Quioscos** de prensa (verde y octogonal, con las revistas a la vista), de helados (toldo de rayas y un cucurucho gigante), de flores (con cubos de ramos delante) y de lotería, en las aceras anchas junto a los cruces. De noche se iluminan. Si chocas con uno, salen volando papeles de colores.
- **Termómetros de calle** en las aceras de las calles principales, con un panel de luces que alterna la hora (la de tu dispositivo), la temperatura (según la hora del día) y la fecha. Algunos llevan encima la cruz verde de farmacia, que parpadea.
- **Fuentes decoradas** en las rotondas que lo parecen: el juego mira la foto aérea de cada isleta y, si es agua azulada o un disco claro de espuma, o si el mapa marca agua dentro, pone una fuente con su vaso de piedra, una fuente de dos tazas en el centro, surtidores en arco (en las grandes), cortinas de agua y un anillo de flores. Suena el agua al acercarte, y de noche se iluminan con luces de colores que van cambiando. En la zona incluida salen 8 fuentes.

Todo esto choca con el coche como las farolas y los árboles.

## Detalles reales

- **Carriles de verdad**: el número de carriles de cada calle sale de OpenStreetMap (unas mil calles lo tienen). Se pintan las líneas entre carriles (discontinuas) y la central (doble continua si hay dos o más carriles por sentido), y el tráfico circula por un carril concreto.
- **Más vehículos en el tráfico**: furgonetas de reparto, camionetas, camiones de basura, ambulancias y camiones de bomberos, menos frecuentes que los turismos y con su largo real (de 5 a 6,5 m).
- **Restos en los choques**: en un golpe fuerte salen volando parachoques, puertas, chapas, alerones, alguna rueda y tuercas; rebotan, se quedan en la calzada y desaparecen a los 40 s.
- **Golpes grabados**: chapa, metal, cristales, madera y plástico grabados de verdad, según contra qué choques (con un golpe grave sintetizado debajo). Si el navegador no puede reproducir `.ogg` (iPhone antiguo), se oyen los sintetizados de antes.
- **Decoración de carrera**: vallas rojas y blancas en los bordillos de la recta de salida, torres con pancartas, banderas a cuadros sobre el arco, una tribuna cubierta si cabe, una carpa en los boxes y conos en los checkpoints.

Las piezas son de Kenney (kenney.nl), con licencia CC0: Car Kit 3.1 (coches de los rivales en `modelos/rivales/`, vehículos de `modelos/trafico/`, restos en `modelos/restos/` y el cono), Racing Kit (`modelos/carrera/`) e Impact Sounds (`sonidos/`). Cada carpeta lleva su licencia; los modelos del Car Kit leen su textura de `Textures/colormap.png`.

Desde el entorno en el que se ha hecho esto no se podía llegar a Poly Haven, ambientCG ni a la API de Overpass de OpenStreetMap (la política de red los bloquea), así que no hay texturas ni cielos fotográficos de esos repositorios.

## Hora del día

**Día**, **atardecer** (sol bajo y naranja, sombras largas) o **noche** (cielo con estrellas y luna, con luz de luna suficiente para ver las calles). Al atardecer y de noche se encienden las ventanas de las casas, las farolas (con su charco de luz en el suelo) y los faros de todos los coches, que alumbran el asfalto por delante: los del jugador llegan a unos 50 m. La elección se recuerda.


En la partida hay tráfico: coches que circulan por la derecha de las calles, frenan si tienen a alguien delante (y pitan si el jugador les corta el paso) y encienden los pilotos al frenar. Si chocas con uno, los dos rebotan según la velocidad y el ángulo del golpe; el de tráfico sale despedido, gira, se queda parado con las luces de emergencia y luego vuelve a su carril. Los peatones van por las aceras y se apartan de un salto si el coche se les echa encima. El siguiente checkpoint (o el destino, en azul) es una columna de luz; los demás solo muestran un aro en el suelo, y arriba a la derecha una flecha indica hacia dónde queda.

El suelo es la ortofoto aérea de la zona (la del paseo), con sus calles, campos y ríos. Encima se dibujan las calles con asfalto, aceras de losetas, bordillos, rigolas, marcas viales, pasos de cebra, farolas y árboles en las avenidas, árboles en parques y praderas, agua (ríos, lagos, humedales y piscinas), quitamiedos de doble onda con postes y captafaros en las curvas de las vías rápidas y rotondas con isleta. Hay cielo con degradado y sol, y en el ordenador sombras de edificios, árboles y coches alrededor del jugador (en el móvil no, para que vaya fluido). Los edificios se dibujan por bloques de 300 m para que solo se pinten los que se ven. Las aceras y los bordillos se cortan donde entran en la calzada de otra calle (cruces, calzadas dobles), y la calzada y las aceras usan texturas de asfalto y adoquín (`texturas/`) generadas con Blender para este proyecto.

### Choques

El coche choca con tres círculos a lo largo de su eje (morro, centro y cola), así que entra por sitios estrechos y los golpes en una esquina lo hacen girar. Cada cosa responde a su manera: las casas y los árboles lo paran y rebota un poco; el quitamiedos lo desvía y lo deja seguir rozando, con chispas; el agua no deja pasar. Subir a la acera de lado y despacio da un bote y la conducción se pone dura (no pasa de 25 km/h); embestir el bordillo de frente y deprisa hace que rebote hacia la calzada. Los golpes fuertes sacuden la cámara y tiñen de rojo los bordes de la pantalla.

### Conducción y sonido

La carrocería cabecea al acelerar y frenar y se inclina en las curvas. Si el asfalto no aguanta el giro, el coche derrapa hacia fuera; al frenar fuerte, derrapar o salir a fondo, los neumáticos chirrían, echan humo y dejan marcas negras en el asfalto, y las luces de freno se encienden. Con la velocidad se abre el ángulo de la cámara.

Todo el sonido se sintetiza en el navegador (Web Audio, sin archivos): el motor sigue las vueltas y el gas (con el corte de inyección y el bajón al cambiar de marcha), la admisión, la rodadura y el aire, el chirrido de las ruedas, el roce de chapa, los golpes (metálicos contra el quitamiedos y las farolas, de madera contra los árboles, con cristales contra otro coche), los botes en el bordillo, las bocinas del tráfico, la cuenta atrás y los checkpoints. El botón 🔊 (o la tecla `N`) lo quita y lo recuerda.

### Controles

Teclado: flechas o WASD. `H` toca el claxon. `C` (o el botón **Cámara**) cambia entre la vista de detrás del coche, la de cerca y la de dentro; la de dentro va a la altura de un asiento alto para ver bien la calle. `M` (o **Menú**) pausa la partida; **Continuar** vuelve a ella.

El juego se abre a **pantalla completa** y en horizontal con el primer toque (los navegadores solo la dejan abrir tras tocar la pantalla). El botón ⛶ de arriba (o la tecla `F`) la quita o la vuelve a poner, y se recuerda. En el iPhone el navegador no deja ponerla; instalado en la pantalla de inicio, el juego se abre sin barras.

El panel de arriba a la izquierda está pensado para el móvil: en una sola línea van la velocidad, la marcha con las revoluciones, la señal de límite (redonda, como las de verdad, parpadea si vas más deprisa) y el daño; debajo, en letra pequeña, el nombre de la calle.

En el móvil, abajo a la izquierda hay una **ruleta**: se gira con el dedo alrededor del centro (140° a cada lado es el giro completo), vibra a cada muesca, un arco ámbar marca cuánto giras y al soltarla vuelve sola al centro. A la derecha están los pedales **GAS** y **FRENO**, grandes y de colores, y el botón **D/R** de la marcha. Los límites de velocidad son solo informativos.

El coche tiene caja automática de seis marchas. Arriba a la izquierda se ven la marcha y el tacómetro: la caja sube hacia las 6200 rpm y baja al acelerar a fondo con pocas vueltas o al frenar hasta casi parar. Para ir **marcha atrás** hay dos maneras:

- Parado, mantén el freno (↓ o **FRENO**): a las tres décimas entra la R y el freno empuja hacia atrás (y el acelerador frena). Para volver, pisa el acelerador: frena, y parado pone la marcha adelante.
- Con el coche casi parado, toca **D/R** (o la tecla `R`): con la R así puesta, el acelerador va hacia atrás y el freno frena, como en un coche automático. Otro toque vuelve a la D.

La física es la de un turismo de unos 150 CV: de 0 a 100 km/h en unos 8 s, frenada de 100 a 0 en unos 45 m y punta de unos 200 km/h. El volante responde menos a más velocidad.

## Instalar en el móvil

Activa GitHub Pages en Settings → Pages, eligiendo la rama del juego y la carpeta raíz. Después abre `https://narcimarquez-eng.github.io/CochesHuelva/carreras_huelva.html` en Chrome y elige la opción de añadirlo a la pantalla de inicio. Hay que abrirlo desde esa URL: desde un archivo local no se puede instalar. El icono está en `icon.svg` y `icon-512.png`.

## Coches

En la pantalla de inicio se elige el coche: el básico (una caja roja), el **SUV compacto**, uno de la galería, CarConcept o ToyCar.

Los rivales del circuito llevan coches de carreras y deportivos de Kenney Car Kit 3.1 (CC0): `race`, `race-future`, `sedan-sports` y `hatchback-sports`.

El SUV compacto (`modelos/coches/suv-compacto.glb`) lo ha aportado el autor del juego. Es el modelo más detallado (58 500 triángulos), así que solo lo conduce el jugador, no sale en el tráfico. Sus propios pilotos LED se encienden al frenar y sus faros LED de noche.

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
