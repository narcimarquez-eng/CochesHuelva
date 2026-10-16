# CochesHuelva
Carrera de coches por Huelva

## Prototipo

`carreras_huelva.html` es un juego de conducción en 3D que corre en el navegador. Usa Three.js y JSZip desde cdnjs, así que necesita conexión. Las fachadas se dibujan con ventanas, puertas y zócalo a partir de los colores del Catastro; las casas sin foto toman el color de una vecina que ya lo tiene o, si no hay, de una paleta sacada de fotos de Huelva.

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
- `foto=REF&tam=640`: la foto de fachada reducida a 640 px de ancho (56 KB en vez de 178 KB), si el servidor tiene la extensión GD de PHP; si no, va la original. El juego la pide así: para el color y el parche basta.

Está probada contra un Catastro de mentira (mismas URL y respuestas), no contra el de verdad, que no se alcanza desde aquí. Con la versión antigua todo sigue funcionando, solo que sin parcelas: el Paseo y el juego lo detectan y lo dicen.

Sin la línea de CORS (`Access-Control-Allow-Origin`), el Paseo en GitHub Pages saca las casas de OpenStreetMap (menos y sin colores). Las zonas de más de 25 km² también salen sin el Catastro, y las muy grandes pueden ir lentas en el móvil (el menú avisa).

### Lo que falta, desde el juego

Al abrir un lugar, el menú dice si le falta algo del Catastro y deja descargarlo sin pasar por el Paseo:

- **Trozos de casas** que no llegaron cuando se preparó la zona.
- **Parcelas** (con el `catastro.php` nuevo).
- **Porches y patios** (ver [Porches y patios](#porches-y-patios)), en las zonas preparadas antes de que el juego los usara. Hay que volver a pedir al Catastro todos los trozos de la zona (unos minutos); de cada uno solo se añaden las partes sin plantas, y las casas quedan igual. Si alguno falla, se pide partido en cuatro, y los que sigan sin llegar se pueden volver a pedir.

**⬇️ Descargar lo que falta** los pide, los mete en la zona, la guarda en este navegador y la vuelve a abrir. Huelva también: su versión completada se guarda y se usa en lugar de la incluida (salvo si es de una versión anterior de la zona incluida: la nueva ya trae los porches y los patios).

### Trozos más pequeños cuando algo falla

El Catastro se pide en trozos de 700 m. Si uno falla (el Catastro tarda demasiado con las zonas con muchas casas), **se vuelve a pedir partido en cuatro** de 350 m, y si alguna de esas partes también falla, otra vez en cuatro: 175 m y luego unos 90 m. Es igual en el Paseo y en **⬇️ Descargar lo que falta** del juego, para las casas y para las parcelas:

- Al volver a descargar una zona, solo se piden los trozos que faltan, y **ya partidos**: lo que sí llegó no se vuelve a pedir.
- Las partes de un trozo que falla se piden enseguida, así que si el Catastro está caído se nota a los pocos intentos y se deja de insistir (los trozos siguen apuntados como pendientes para la próxima vez).
- Lo mismo con OpenStreetMap en el Paseo: si la consulta de toda la zona no llega, se pide por cuartos (y los cuartos que fallen, otra vez por cuartos; si los dos primeros tampoco llegan, el servidor no está y no se insiste), y si aun así falta un trozo el informe lo dice y se vuelve a pedir al preparar la zona otra vez.

Al guardar la zona completada, el juego reaprovecha comprimido lo que no cambia, así que el zip sigue pesando lo mismo y se guarda en segundos.

Los **colores y la textura de las fachadas** se piden solos **mientras conduces**, antes de llegar a cada casa, y se ponen sin que se note:

- **Qué se pide**: las casas que vas a ver. Primero las del camino de delante, hasta 240 m y por orden de llegada: la vuelta del circuito, la ruta de la exploración (o la roja, si te has salido) y, siempre, lo que tienes delante del morro, por si no sigues la ruta; y las de alrededor del coche. Tres a la vez.
- **Cuándo se pinta**: si la casa no está en pantalla (detrás, a un lado), al momento. Si se ve, el color de antes se funde poco a poco con el nuevo: en menos de un segundo si se ve pequeña, a lo lejos, y despacio (hasta 4 s) si se ve grande; un cambio así de lento no se nota. Si deja de verse a medio fundido, se termina de golpe. La textura de la foto también se funde, y solo se sube a la tarjeta gráfica el trozo de la malla de esa casa.
- **Mientras no llega su foto**, la casa lleva el color de una vecina que ya lo tiene, a menos de 160 m (las de una manzana suelen ser de la misma época y estilo), mejor cuanto más cerca y si es de su misma altura (casa baja o bloque). Si hay pocas vecinas con color, a veces sale de la paleta, que ahora es la de 277 fotos de fachada de Huelva, distinta para casas bajas (más cal blanca y ocre) y bloques (más ladrillo). Así, cuando llega la foto, el cambio suele ser pequeño.

Su foto de fachada se analiza igual que en el Paseo. Los colores se guardan en este navegador por referencia catastral, así que la próxima vez ya están, en esa zona o en otra que tenga las mismas casas. Si `catastro.php` no contesta, deja de pedir. Con el análisis nuevo (versión 6), las casas con colores del análisis anterior se vuelven a pedir al pasar por ellas y se repintan igual, sin que se note.

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

Las rutas, el circuito y el tráfico van por la **red de calles**: cada calle de OpenStreetMap se parte en todos los puntos que comparte con otra, porque en el mapa casi todos los cruces son un punto en mitad de una de las calles y muchas rotondas son un anillo de una sola pieza. Antes la red solo unía las calles por sus extremos: en la zona de Huelva quedaban unidos 168 de sus 313 km de calles, las rutas daban rodeos (de media, 2,1 veces la distancia en línea recta) y los coches de tráfico se daban la vuelta o desaparecían en mitad de los cruces. Ahora están unidos 297 km (todo lo que tiene paso), las rutas miden de media 1,5 veces la línea recta y casi ningún coche se queda sin salida. Fuera de la red, aunque se dibujan y se puede conducir por ellas, quedan los pasillos de los aparcamientos, las entradas de garaje y las calles privadas, prohibidas o solo con permiso (213 en Huelva): ni la ruta ni el tráfico pasan por ahí.

## Casas como en el Paseo

Las casas se construyen igual que en el Paseo por el relieve:

- **Teja árabe**: tejado a dos aguas en la crujía de la fachada principal (toda la casa si tiene menos de 11 m de fondo; si no, 6,2 m y el resto plano). Lleva alero en la fachada, y atrás si la casa es poco honda, y las hileras de tejas dibujadas.
- **Azotea**: pretil de casi un metro con albardilla y, en algunas casas medianas, el torreón de la escalera.
- **Patios**: los de dentro de la casa (huecos en el Catastro) tienen sus paredes con ventanas y no los tapa el tejado.
- **Tejado de la foto**: el color de cada tejado se saca de la foto aérea, y la foto se ve encima con las tejas.
- **¿Teja o azotea?**: lo decide un modelo hecho con casas de Huelva de verdad (ver [Teja o azotea](#teja-o-azotea)).

Las fachadas tienen ventanas con persianas y rejas, balcones, puertas, zócalo y recercados de albero. Sus colores salen de la foto de fachada del Catastro, analizada en el Paseo.

### Colores de la foto de fachada

El análisis de la foto (en el Paseo y en el juego) agrupa los píxeles por **tono**, la proporción entre rojo, verde y azul, que casi no cambia entre la parte de la pared al sol y la parte a la sombra:

- **Balance de blancos**: lo más claro que no es cielo ni tiene color vivo debería ser blanco. Si sale azulado, la casa está a la sombra (la alumbra el cielo) y se le quita ese azul, como mucho un 15 %. Al sol no se toca.
- **Exposición**: muchas fotos se hacen contra el cielo y la casa sale oscura. Se aclara la foto entera por igual (hasta un 45 %), no cada color por su cuenta: el ladrillo sigue siendo más oscuro que la cal.
- **Ocho grupos** de tono, para que el blanco, el crema, el ocre, el ladrillo y el gris de la calle no se mezclen. Dos grupos son la misma pared (la parte al sol y la de sombra) si tienen el mismo matiz y una saturación parecida; los grises y blancos, si están muy cerca, perdonando a la parte más oscura un poco de azul. Antes bastaba con el mismo matiz, y el blanco cálido de los marcos se juntaba con el ladrillo: salía rosa pálido o blanco.
- Cada pared empieza por su parte más clara y suma sus partes a la sombra. Lo gris pesa menos que lo que tiene color, porque gris son también la calle, los cristales y el cielo con calima; lo blanco azulado muy claro es cielo. También cuentan menos la parte de abajo de la foto (calle y coches) y los lados (la casa de la parcela suele estar en el centro). El ladrillo, aunque es oscuro, no se castiga por serlo. El cielo blanco quemado o nublado de arriba se quita como el azul.
- **El color** es el tono mediano de toda la pared con la saturación de sus píxeles más puros (en el ladrillo, las juntas y los marcos blancos lo apagan), y el brillo de su parte al sol. La cal blanca sale blanca aunque esté a la sombra. El zócalo y la carpintería llevan la misma corrección de exposición.
- Las hojas al sol, de verde amarillento, no cuentan como pared.

Comparado con el análisis anterior en 277 fotos de Huelva: 32 casas que salían blancas o rosa pálido ahora salen de ladrillo, ocre, salmón o crema, como en la foto, y 12 que salían de color ahora salen blancas (casi siempre el color venía de los marcos o de la casa de al lado).

**La casa en tres bandas**: en la foto se busca la casa. Arriba, la primera fila de pared bajo el cielo (el tejado o la cornisa); abajo, donde acaba la pared. Si entre las dos no hay suficiente pared, no se toma como casa: será cielo, árboles o un coche, y la casa se queda con su color. Entre esos dos bordes hay tres bandas:

- **alta**: el último quinto (cornisa, pretil, ático);
- **media**: la pared;
- **baja**: el zócalo, si lo hay.

De cada banda sale su parche de textura, y en el juego se pone cada uno a su altura: el zócalo abajo, la pared en medio y la banda alta en el último quinto de la casa, con el pretil. El parche alto guarda además si esa banda es de otro color que la pared. En las casas bajas, el **alto del zócalo** sale también de la foto. En las 157 fotos de Huelva (con el análisis de la versión 6): 147 casas reconocidas, 77 parches de pared, 96 de banda alta y 14 de zócalo.

**Textura de la pared**: además de los colores, de cada foto se saca un **parche** de 64 × 64 de la pared real. Es el cuadrado (un 9 % del ancho de la foto) que es casi todo pared, sin cielo y lo más uniforme posible: sin ventanas, esquinas ni hojas. Si aun así varía demasiado, no se usa y la casa se queda con su color. El parche se guarda con la media de cada color en gris medio, así que solo lleva la textura (ladrillo, azulejo, enfoscado, piedra), y el color sigue siendo el de la pared. En el juego se repite por **todas las paredes de la casa**, cada 1,6 m y en espejo para que no se vean costuras, debajo de las ventanas y puertas dibujadas, y se difumina de lejos. En las 157 fotos de Huelva, 77 dan parche de pared y 111 alguno de los tres. Pesa poco más de 1 KB y va en `zona.json` con los colores; el juego los junta en una sola textura de 2048 × 2048, en casillas de 48 px (hasta 1.764 parches).

Antes solo se miraban los píxeles claros, así que el ladrillo y las paredes rojas en sombra quedaban fuera y salía blanco o gris. En el Paseo, los colores guardados con un análisis anterior se rehacen solos desde la foto guardada, sin descargar nada; en el juego, se vuelven a pedir al pasar.

### Teja o azotea

Antes lo decía el color de la foto aérea: rojizo y de brillo medio era teja. Para ver si funcionaba se han mirado a mano **214 casas de una y dos plantas de Huelva** con su foto de fachada del Catastro (en ella se ve si arriba hay una fila de tejas o un pretil): acertaba el 58 %, lo mismo que decir siempre azotea, y sobre todo ponía teja en azoteas con pretil. La foto aérea tiene unos 2 m por píxel, así que en una casa de 6 m apenas hay tres píxeles, mezclados con la calle y con el tejado de al lado.

Lo que sí distingue una cosa de otra, con esas casas:

- **La parcela**: las casas con patio o jardín (las que dejan sin construir buena parte de su parcela) suelen ser de teja, y las que la llenan, de azotea (con menos del 75 % construido, 6 de cada 10 son de teja; con más, 1 de cada 4). Las muy largas (naves, comercios, bloques en hilera) suelen ser de azotea.
- **La foto de fachada**, si ya se ha mirado: la franja de arriba de la casa, justo bajo el cielo, es más oscura que la pared cuando hay teja (las tejas y la sombra del alero, aunque estén encaladas), con una línea oscura debajo y a menudo rojiza; con azotea, el pretil es de la pared, y por encima asoman antenas, barandas y depósitos, así que el borde contra el cielo es más irregular.

Con eso se han ajustado regresiones logísticas (con validación cruzada: cada casa se prueba con un modelo hecho sin ella):

| Con qué se decide | Acierto |
|---|---|
| El color de la foto aérea (antes) | 58 % |
| Solo lo larga que es la casa (zonas sin parcelas) | 62 % |
| La parcela y lo larga que es | 72 % |
| La foto de fachada, la parcela y lo larga que es | 78 % |

Antes de usarlos se probaron en otras 101 casas que no se habían usado para elegirlos: con la parcela, 71 % (el color de la foto aérea, 59 %). Las casas de cuatro plantas o más y las de más de 700 m² siguen siendo de azotea, y las de OpenStreetMap siguen con su dato o con el color de la foto.

- **Al abrir la zona**, cada casa del Catastro sale con lo que dicen su parcela y su largo (o su foto, si ya se miró antes en este navegador o en el Paseo). En Huelva quedan de teja un 35 % de las casas bajas (en las fotos eran un 46 %: el modelo, cuando duda, prefiere azotea).
- **Mientras conduces**, cuando llega la foto de fachada de una casa (las que se piden para el color), también se miran esos rasgos del tejado. Si con ellos cambia de teja a azotea o al revés, su bloque de casas se rehace con el tejado nuevo cuando no se nota: con la casa fuera de la vista o pequeña, a lo lejos, sin fundidos de color a medias en el bloque y un bloque por segundo como mucho (rehacer uno de 190 casas lleva unos 40 ms). Los rasgos se guardan con los colores, así que la próxima vez ya sale bien. Por eso el análisis de la foto pasa a la versión 7: las casas con colores de la versión 6 se vuelven a pedir al pasar por ellas.
- El Paseo decide igual, y su análisis de la foto guarda también los rasgos del tejado (van en `zona.json` con los colores).

### Plano y foto al tocar una casa

En el menú, la opción **Casas** (desactivada al principio) hace que, durante la partida, al tocar una casa (o hacer clic en ella) se abra su ficha: la **foto de la fachada** del Catastro y el **plano** de la casa y de su parcela. El plano se dibuja en metros, con el norte arriba: la parcela del Catastro a trazos, la casa en amarillo, las casas de alrededor y las calles con su anchura, además de la escala y la superficie de la planta y de la parcela. Como el plano sale de los contornos que ya trae la zona, funciona aunque el Catastro no responda; solo la foto necesita la red.

Mientras la ficha está abierta, la partida se para (el coche no se mueve y el tiempo de carrera no cuenta) y sigue al cerrarla con ×, con Esc o con B o A del mando. Las casas que no vienen del Catastro (de OpenStreetMap o inventadas) tienen plano, pero no foto. El plano no es el oficial que da la Sede del Catastro (de allí no se puede sacar desde el juego): es el dibujo de sus contornos.

## Porches y patios

El Catastro da cada casa en **partes**, cada una con sus plantas sobre y bajo rasante. Las que no tienen ninguna planta sobre rasante el Paseo y el juego las tiraban; ahora se guardan (en `casas.geojson`, con `plantas: 0` y `tipo`) y se dibujan:

- **Porche** (`tipo: porche`, sin sótano): son porches, cobertizos, galerías y marquesinas, cubiertos y sin paredes (están dentro del contorno del edificio). Llevan un **tejadillo sobre pilares**: si van pegados a una casa de teja, de teja a un agua, de 2,45 m por fuera a lo que dé la pendiente junto a la pared, sin pasar del alero; pegados a una casa de azotea, una losa plana a 2,9 m; sueltos, una losa a 2,7 m, y las marquesinas grandes a 4,2 m. Por los lados que no apoyan en una pared van la viga y los **pilares**, en las esquinas y repartidos entre ellas (a 3,3 m como mucho; 5 m en las marquesinas), del color de la casa y con zócalo. Los pilares chocan como una pared; debajo del tejadillo el coche pasa. El Catastro también da sin plantas explanadas, pistas y solares de varias hectáreas: se cubren las de hasta 400 m² y, más grandes, las alargadas (como las marquesinas de los andenes de la estación), de hasta 12 m de ancho y 4.000 m²; las demás se quedan como suelo, y las que pisan una calzada no se ponen.
- **Patio** (`tipo: patio`, con sótano debajo): son sobre todo los patios de luces de los bloques, encima del garaje, alguna rampa y alguna plaza sobre un aparcamiento. Quedan abiertos, con las paredes de la casa alrededor.

En Huelva son 135 porches (78 cubiertos, con 1.035 pilares) y 1.810 patios. El Paseo los descarga y los guarda en la zona, pero en su vista 3D todavía no los dibuja: solo el juego.

**Solería**: los patios llevan baldosas: los del Catastro, los de dentro de las casas, el suelo de los porches y, con parcelas y foto aérea, lo que queda sin construir de las parcelas con casa de hasta 2.000 m² y con al menos un 40 % construido (los patios de las casas y de los bloques; los jardines de los chalets, no). Cada patio, su solería, alineada con su lado más largo: barro, terrazo, losa clara, mármol blanco o hidráulica en damero. La foto aérea, que es borrosa, solo le da luces y sombras, y donde la foto es verde (césped, árboles) no se enlosa: se ve el suelo con su hierba.

El Catastro numera las partes de cada respuesta a su manera (la «part4» de un trozo no es la de otro), así que al juntar los trozos las partes se reconocen ahora por su forma y no por su número. En la zona incluida había 472 casas repetidas (la misma pieza con dos números) que se dibujaban dos veces; ya no están, y en las zonas preparadas antes el juego descarta las repetidas al cargarlas.

## Relieve de la zona

El juego usa las alturas del terreno de la zona (`alturas.asc`, del MDT), así que el suelo, las calles y lo que hay sobre ellas siguen el desnivel:

- **Suelo**: malla de unos 20 m con altura interpolada entre las cuatro celdas más cercanas. La foto aérea y las calles tienen puntos cada 7,2 m (como la rejilla de cotas), para que la foto no asome sobre la calzada ni la calzada se hunda entre dos puntos. El coche va por la altura del suelo y la cámara la sigue; la carrocería se inclina con la cuesta.
- **Casas a nivel**: la planta está a la altura del suelo de la puerta (o la media de la fachada si no hay puerta). Las paredes bajan hasta el punto más bajo de la huella, así que cuesta abajo asoma el basamento y cuesta arriba la casa se mete en la ladera; el alero siempre queda por encima del terreno. Entre una casa y otra, la calle escalona como en la realidad.
- **Porches**: a la altura de la planta de su casa; los pilares bajan hasta el terreno que tienen debajo.
- **Lo que va sobre el suelo**: tráfico, peatones, ciclistas, perros, árboles, farolas, restos de los choques, proyectiles, la línea de la ruta y, en las carreras, los arcos, las balizas, la tribuna, los boxes y el podio.
- **Lo que no cambia**: el agua va plana, y la física no tiene en cuenta la cuesta (la velocidad y la potencia son las mismas en una subida que en llano; solo cambia lo que se ve). Sin datos de altura, todo queda a cero como antes.

## Modo ruta

En el menú hay tres maneras de poner una ruta:

- **De → a**: se escribe o elige una localidad. La lista trae ocho centros; cualquier otra de España se busca con Nominatim (OpenStreetMap). **Calcular ruta** pide el camino por carreteras reales a OSRM (`router.project-osrm.org`). Con él se dibuja una franja de 300 m a cada lado y se abre el Paseo, que la prepara sola como una zona dibujada (calles, foto aérea, relieve, árboles y casas del Catastro) y vuelve al juego con la ruta puesta. La ruta queda guardada con su zona: al abrirla otra vez desde **Lugares**, la carrera sigue esa ruta.
- **Ruta de ejemplo**: `rutas/ejemplo-huelva.gpx`, una avenida de Huelva de 1,6 km.
- **Ruta** (GPX): un fichero de Google Maps, OpenStreetMap u otro, dentro de una zona ya cargada.

La zona se construye solo a lo largo del camino: casas, calles, árboles y farolas a 350 m a cada lado. El coche empieza al principio de la ruta, mirando hacia ella, y el destino es el final; al llegar, se acaba la partida. **Quitar ruta** vuelve a la zona completa.

- **La franja** se saca de una rejilla (celdas de unos 37 m) con todo lo que queda a 300 m de la ruta; su borde de fuera es el polígono que se pide. Antes era un borde desplazado a cada lado, que en las curvas cerradas y en las vueltas se cruzaba consigo mismo: OpenStreetMap y el Catastro dejaban fuera esos trozos y la ruta se quedaba sin carretera. Probado con una horquilla, una ruta de 20 km con curvas y una con lazos: sin cruces y con todos los puntos de la ruta dentro.
- **El Catastro, poco a poco**: el Paseo solo trae las casas de los primeros 5 km; el resto se pide mientras se conduce, 1,5 km por delante del coche, y se añade a la zona sin parar la partida.
- **OpenStreetMap, más deprisa**: con Catastro, el Paseo ya no pide los edificios de OpenStreetMap (no se usan y eran la mitad de la descarga; si al final el Catastro no da casas, se piden aparte), y pregunta a los tres servidores de Overpass a la vez: vale el primero que da un mapa completo y a los demás se les corta.

## Casas que cortan calles## Casas que cortan calles

Algunas casas del Catastro caen encima de una calzada: son sobre todo pasajes bajo edificios (calles que en OpenStreetMap pasan por debajo de una casa) o datos que no casan. En el juego el coche iría contra una pared en mitad de la calle, así que al cargar la zona se comprueba el eje de cada calle y dos líneas a un cuarto de su ancho, cada metro y medio: si una casa tiene dos o más puntos de calzada dentro, no se pone. El menú avisa de cuántas se han quitado y en qué calles (la lista entera sale al pasar el ratón por el aviso y en la consola). En la zona incluida son 97 de 31 910.

## Vallas de las casas

**Con las parcelas del Catastro** (`parcelas.geojson`: la zona incluida las trae, 10.444; en otras, del Paseo o con «Descargar lo que falta»), se pone valla en **toda parcela con casa y algo de jardín o patio**:

- una casa de hasta tres plantas;
- al menos 25 m² sin construir;
- menos del 75 % de la parcela construido.

La valla va por la **linde** (15 cm por dentro), salvo donde la propia casa o la del vecino hacen de valla, y no se repite donde ya la puso el vecino. En Huelva son unas 1.360 casas. La cancela de hierro va en el lado más cercano a la calle.

**Sin parcelas**, se deduce qué casas son chalets o casas con jardín. Tiene que ser una casa (o un grupo de hasta seis piezas que se tocan):

- suelta, sin otra a menos de 2 m;
- de hasta tres plantas y 40–900 m² de planta;
- con sitio alrededor y a menos de 35 m de una calle.

En OpenStreetMap, además, tiene que ser una casa (`house`, `detached`, `villa`…). Su parcela se calcula: el rectángulo que la envuelve se abre de 2,5 a 5,5 m por cada lado, el lado de la calle llega **hasta la acera**, y cada lado se acerca a la casa hasta que no pisa la calzada, la acera, una piscina o la casa de al lado.

Las vallas miden **2 m**, como las de verdad. Hay tres tipos:

- **muro** de 2 m;
- **murete** de 1 m con 1 m de **reja** de barrotes encima;
- **murete** de 0,9 m con **seto** recortado hasta los 2 m.

Los muretes son blancos, crema, albero o ladrillo, con albardilla y pilares, y la cancela mide 2 m. Las vallas chocan como un muro (el coche se abolla) y ningún árbol queda en mitad de una.

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

- **Aceras**: las calles de pueblo con la categoría «calle con aceras» llevan acera aunque el Paseo no las haya medido (de 1,5 a 2,2 m, según el tipo). Las calles de servicio y los aparcamientos no llevan aceras. Las calles que solo tienen sitio para un carril (menos de 7,5 m entre fachadas) se dibujan de un solo sentido, con una calzada de 3,3 m y aceras con lo que sobra.
  - **Hasta la fachada**: el ancho de cada acera se mide cada 3 m y a cada lado, desde el bordillo hasta la fachada o la valla más cercana. Si está a menos de 4 m más allá del ancho de la calle, la acera llega hasta ella (ya no queda una franja de tierra entre la acera y las casas); si está más cerca, la acera no se mete en la casa. Donde no hay nada cerca (un solar, una plaza) lleva el ancho de la calle, y nunca entra en la calzada de otra calle. El bote del bordillo usa ese mismo ancho.
  - **Esquinas**: en cada punto que comparten dos o más calles (sus extremos o un vértice en mitad de una de ellas, que es como vienen casi todos los cruces de OpenStreetMap), entre cada dos calles seguidas hay una esquina. En la de una manzana, la acera dobla la esquina con el bordillo redondeado (radio de 2,5 a 5 m según lo anchas que sean las calles, menor si la acera es estrecha o la calle corta) y la curva del bordillo es asfalto; antes las aceras se cortaban 4 m antes del cruce y la esquina quedaba vacía. En la parte de fuera de una curva o de una calle en «L», la calzada y la acera siguen alrededor del cruce sin hueco. Si solo una de las dos calles tiene acera, esta llega hasta el borde de la otra calzada. En Huelva son unas 1.970 esquinas de manzana y 135 de fuera.
  - Para no cargar el móvil, la acera solo se parte donde su ancho cambia, y las curvas van en trozos de metro y medio: la malla de calles tiene unos 740.000 vértices (antes, 537.000).
  - **Calles que se quedaban sin acera**: el Paseo decide si una calle es urbana mirando si tiene casas pegadas, así que las avenidas anchas, las calles de chalets con la casa retranqueada y las que bordean un parque salían como «carretera con arcén», sin acera. Ahora el juego, al cargar la zona, mira también las **parcelas urbanas del Catastro** (las de referencia urbana, no las rústicas): si más de la mitad de la calle tiene una parcela urbana a 15 m o una casa a 25 m, lleva acera a los dos lados. Solo en calles de pueblo (principales, secundarias, terciarias, residenciales y sin clasificar), nunca en autovías, enlaces, túneles ni caminos. En la zona incluida son 678 calles más, unos 70 km de acera. El Paseo, al montar una zona nueva, también da por urbana una calle si tiene parcelas urbanas a menos de 15 m en la mitad de su recorrido, así que esas calles ya vienen con acera medida.
  - **Rotondas**: el anillo de la rotonda lleva acera solo por fuera; por dentro está la isleta, sin acera ni bordillo de paseo. Los peatones van por la de fuera.
- **Calles de sentido único**: se respeta el sentido de OpenStreetMap (`oneway=yes`, y también `oneway=-1`, que es de sentido único pero al revés de como está dibujada la calle). Las rotondas y las autovías son de sentido único aunque no lo digan. Los coches de tráfico y los ciclistas ya salen colocados en el sentido de la calle: antes, al empezar la partida, la mitad de los que caían en una calle de sentido único venían de frente.
  - **Lo que no se puede saber**: si OpenStreetMap no dice que una calle es de sentido único, el juego la trata como de doble sentido, aunque en la realidad no lo sea. Las muy estrechas (menos de 7,5 m entre fachadas) solo caben con un carril y van en el sentido en que está dibujada la calle, que es a ojo: el tráfico no entra en ellas si tiene otro camino y las rutas las evitan (cuentan el triple). En Huelva son 12. Para arreglar una calle, se le pone el sentido en [openstreetmap.org](https://www.openstreetmap.org) y se vuelve a descargar la zona en el Paseo.
  - Da igual que la zona sea nueva o de antes: el sentido se calcula al abrirla en el juego, con los datos de OpenStreetMap que guarda la zona.
- **Carriles de verdad**: el número de carriles de cada calle sale de OpenStreetMap (unas mil calles lo tienen). Se pintan las líneas entre carriles (discontinuas) y la central (doble continua si hay dos o más carriles por sentido), y el tráfico circula por un carril concreto.
- **Puentes y pasos elevados**: los tramos que OpenStreetMap marca como `bridge=yes` (o `layer=1`) van sobre un tablero a 5,5 m del suelo, con una rampa de 25 m en cada extremo. Se ven elevados, y el coche sube al tablero al entrar por un extremo de frente (a 5 m como mucho), con la cámara a su altura. En el tablero no choca con lo que hay debajo (ni con ciclistas ni peatones), no deja huellas ni humo en el suelo de abajo, los costados son una barandilla que lo devuelve al centro, y sale por el otro extremo (o vuelve atrás si da la vuelta). El tráfico no sube a los puentes. Los pasos inferiores (`layer=-1`) no se tratan: el suelo de debajo sigue igual.
- **Más vehículos en el tráfico**: furgonetas de reparto, camionetas, camiones de basura, ambulancias y camiones de bomberos, menos frecuentes que los turismos y con su largo real (de 5 a 6,5 m).
- **Restos en los choques**: en un golpe fuerte salen volando parachoques, puertas, chapas, alerones, alguna rueda y tuercas; rebotan, se quedan en la calzada y desaparecen a los 40 s.
- **Golpes grabados**: chapa, metal, cristales, madera y plástico grabados de verdad, según contra qué choques (con un golpe grave sintetizado debajo). Si el navegador no puede reproducir `.ogg` (iPhone antiguo), se oyen los sintetizados de antes.
- **Decoración de carrera**: vallas rojas y blancas en los bordillos de la recta de salida, torres con pancartas, banderas a cuadros sobre el arco, una tribuna cubierta si cabe, una carpa en los boxes y conos en los checkpoints.

Los árboles salen de ez-tree (MIT) con texturas de Poly Haven y TextureCan (CC0), en `vendor/` y `texturas/arboles/`. Las demás piezas son de Kenney (kenney.nl), con licencia CC0: Car Kit 3.1 (coches de los rivales en `modelos/rivales/`, vehículos de `modelos/trafico/`, restos en `modelos/restos/` y el cono), Racing Kit (`modelos/carrera/`) e Impact Sounds (`sonidos/`). Cada carpeta lleva su licencia; los modelos del Car Kit leen su textura de `Textures/colormap.png`.

Desde el entorno en el que se ha hecho esto no se podía llegar a Poly Haven, ambientCG ni a la API de Overpass de OpenStreetMap (la política de red los bloquea), así que no hay texturas ni cielos fotográficos de esos repositorios.

## Hora del día

**Día**, **atardecer** (sol bajo y naranja, sombras largas) o **noche** (cielo con estrellas y luna, con luz de luna suficiente para ver las calles). Al atardecer y de noche se encienden las ventanas de las casas, las farolas (con su charco de luz en el suelo) y los faros de todos los coches, que alumbran el asfalto por delante: los del jugador llegan a unos 50 m. La elección se recuerda.


En la partida hay tráfico: coches que circulan por la derecha de las calles al límite de cada una (el de OpenStreetMap; si no lo tiene, 30 a 50 km/h en la ciudad y, en las carreteras fuera de poblado, 90 en las nacionales y comarcales, 80 en las locales y 100 en las vías rápidas), frenan si tienen a alguien delante (más lejos cuanto más deprisa van) (y pitan si el jugador les corta el paso) y encienden los pilotos al frenar. Si chocas con uno, los dos rebotan según la velocidad y el ángulo del golpe; el de tráfico sale despedido, gira, se queda parado con las luces de emergencia y luego vuelve a su carril. Los peatones van por las aceras y se apartan de un salto si el coche se les echa encima. El siguiente checkpoint (o el destino, en azul) es una columna de luz; los demás solo muestran un aro en el suelo, y arriba a la derecha una flecha indica hacia dónde queda.

El suelo es la ortofoto aérea de la zona (la del paseo), con sus calles, campos y ríos. Como la foto tiene unos 2 m por píxel y de cerca se ve borrosa, **donde parece césped, arena o tierra lleva encima su textura de detalle** (`texturas/cesped.jpg`, `arena.jpg` y `tierra.jpg`, hechas con `texturas/generar_suelos.py`):

- **Qué es cada punto** lo dice su color: si manda el verde, césped (también el verde oliva de las marismas); si es un tono cálido, claro y poco saturado, arena; si es cálido y más oscuro, tierra. Ni la teja ni lo gris (calles, plazas, tejados) llevan nada.
- **Las capas del mapa ayudan** donde el color engaña: los parques, jardines y praderas son césped aunque en verano estén secos (salvo lo gris, como plazas y caminos), los cultivos son tierra y los arenales, arena.
- Las texturas tienen de media gris medio, así que **no cambian el color de la foto**: solo le añaden las briznas y las matas del césped, los granos, conchas y ondas del viento de la arena, y las piedras, grietas y matas de hierba seca de la tierra. Van a dos escalas (en el móvil, a una) para que no se note la repetición y de lejos se funden con la foto.
- Sin foto aérea, lo mismo sobre el suelo pintado con el mapa; y las isletas de las rotondas y los jardines de los chalets llevan césped. Encima se dibujan las calles con asfalto, aceras de losetas, bordillos, rigolas, marcas viales, pasos de cebra, farolas y árboles en las avenidas, árboles en parques y praderas, agua (ríos, lagos, humedales y piscinas), quitamiedos de doble onda con postes y captafaros en las curvas de las vías rápidas y rotondas con isleta. Hay cielo con degradado y sol, y en el ordenador sombras de edificios, árboles y coches alrededor del jugador (en el móvil no, para que vaya fluido). Los edificios se dibujan por bloques de 300 m para que solo se pinten los que se ven. Las aceras y los bordillos se cortan donde entran en la calzada de otra calle (calzadas dobles, calles que se cruzan sin juntarse) y doblan las esquinas de los cruces, y la calzada y las aceras usan texturas de asfalto y adoquín (`texturas/`) generadas con Blender para este proyecto.

### Choques

El coche choca con tres círculos a lo largo de su eje (morro, centro y cola), así que entra por sitios estrechos y los golpes en una esquina lo hacen girar. Cada cosa responde a su manera: las casas y los árboles lo paran y rebota un poco; el quitamiedos lo desvía y lo deja seguir rozando, con chispas; el agua no deja pasar. Subir a la acera de lado y despacio da un bote y la conducción se pone dura (no pasa de 25 km/h); embestir el bordillo de frente y deprisa hace que rebote hacia la calzada. Los golpes fuertes sacuden la cámara y tiñen de rojo los bordes de la pantalla.

### Conducción y sonido

La carrocería cabecea al acelerar y frenar y se inclina en las curvas. Si el asfalto no aguanta el giro, el coche derrapa hacia fuera; al frenar fuerte, derrapar o salir a fondo, los neumáticos chirrían, echan humo y dejan marcas negras en el asfalto, y las luces de freno se encienden. Con la velocidad se abre el ángulo de la cámara.

Todo el sonido se sintetiza en el navegador (Web Audio, sin archivos): el motor sigue las vueltas y el gas (con el corte de inyección y el bajón al cambiar de marcha), la admisión, la rodadura y el aire, el chirrido de las ruedas, el roce de chapa, los golpes (metálicos contra el quitamiedos y las farolas, de madera contra los árboles, con cristales contra otro coche), los botes en el bordillo, las bocinas del tráfico, la cuenta atrás y los checkpoints. El botón 🔊 (o la tecla `N`) lo quita y lo recuerda.

### Controles

Teclado: flechas o WASD. `H` toca el claxon. `C` (o el botón **Cámara**) cambia entre la vista de detrás del coche, la de cerca y la de dentro. La de dentro tiene los ojos de un conductor alto (1,85 m sobre el suelo, en su asiento) y mira un poco hacia abajo: abajo se ven el capó y las aletas del coche que llevas, y el salpicadero. La carrocería se recorta por la base del parabrisas, así que el techo y los montantes no tapan la calle. `M` (o **Menú**) pausa la partida; **Continuar** vuelve a ella.

**Mando** (Bluetooth o de cable: Xbox, PlayStation o cualquiera que el navegador reconozca, en el móvil y en el ordenador). Se conecta al teléfono u ordenador como siempre y el juego lo ve en cuanto se pulsa uno de sus botones (los navegadores no lo enseñan antes); sale el aviso «Mando conectado». Con él, la ruleta y los pedales de la pantalla se esconden (vuelven al tocar la pantalla).

| En la partida | Botón |
|---|---|
| Acelerar (a medias con el gatillo) | gatillo derecho (RT / R2) o A (✕) |
| Frenar y, parado, marcha atrás | gatillo izquierdo (LT / L2) o X (□) |
| Girar (fino cerca del centro) | stick izquierdo o cruceta |
| Claxon | B (○) o RB / R1 |
| Cámara | Y (△) |
| Marcha D/R | LB / L1 |
| Menú (pausa) | Start / Options o Select / Share |

En los menús, la cruceta o el stick van de botón en botón (el elegido se recuadra), **A** lo pulsa, **B** sigue la partida pausada y **Start** sigue o empieza una nueva. En los golpes el mando vibra, si el mando y el navegador lo permiten.

El juego se abre a **pantalla completa** y en horizontal con el primer toque (los navegadores solo la dejan abrir tras tocar la pantalla). El botón ⛶ de arriba (o la tecla `F`) la quita o la vuelve a poner, y se recuerda. En el iPhone el navegador no deja ponerla; instalado en la pantalla de inicio, el juego se abre sin barras.

El panel de arriba a la izquierda está pensado para el móvil: en una sola línea van la velocidad, la marcha con las revoluciones, la señal de límite (redonda, como las de verdad, parpadea si vas más deprisa) y el daño; debajo, en letra pequeña, el nombre de la calle.

En el móvil, abajo a la izquierda está la **dirección**: una barra con una pastilla que se desliza a los lados. Se toca en cualquier sitio de la barra y cuenta lo que se mueve el dedo desde ahí (la pastilla no salta), así que no hay que dar vueltas como con un volante. Es firme: con poco desplazamiento gira poco (para corregir en recta sin bandazos) y el giro completo está al final del recorrido. La pastilla sigue al dedo con suavidad, vibra cada cuarto de recorrido (más en el centro), una barra ámbar marca cuánto giras y al soltarla vuelve despacio al centro. Con las flechas o el mando, se mueve sola para mostrar hacia dónde va el coche. A la derecha están los pedales **GAS** y **FRENO**, grandes y de colores, y el botón **D/R** de la marcha. Los límites de velocidad son solo informativos.

El coche tiene caja automática de seis marchas. Arriba a la izquierda se ven la marcha y el tacómetro: la caja sube hacia las 6200 rpm y baja al acelerar a fondo con pocas vueltas o al frenar hasta casi parar. Para ir **marcha atrás** hay dos maneras:

- Parado, mantén el freno (↓ o **FRENO**): a las tres décimas entra la R y el freno empuja hacia atrás (y el acelerador frena). Para volver, pisa el acelerador: frena, y parado pone la marcha adelante.
- Con el coche casi parado, toca **D/R** (o la tecla `R`): con la R así puesta, el acelerador va hacia atrás y el freno frena, como en un coche automático. Otro toque vuelve a la D.

La física es la de un turismo de unos 150 CV: de 0 a 100 km/h en unos 8 s, frenada de 100 a 0 en unos 45 m y punta de unos 200 km/h. El volante responde menos a más velocidad, pero el agarre es de juego de conducción (unos 1,3 g): a 40 km/h el radio de giro a volante completo es de unos 10 m y a 60 km/h de unos 21 m.

## Instalar en el móvil

Activa GitHub Pages en Settings → Pages, eligiendo la rama del juego y la carpeta raíz. Después abre `https://narcimarquez-eng.github.io/CochesHuelva/carreras_huelva.html` en Chrome y elige la opción de añadirlo a la pantalla de inicio. Hay que abrirlo desde esa URL: desde un archivo local no se puede instalar. El icono está en `icon.svg` y `icon-512.png`.

## Coches

En la pantalla de inicio se elige el coche: el básico, el **SUV compacto**, uno de la galería, CarConcept o ToyCar.

El básico es un turismo a escala real (4,5 m de largo, 1,8 m de ancho, 1,46 m de alto y ejes a 2,7 m) hecho en el propio juego, sin archivo de modelo. La carrocería es una superficie suave que se construye con anillos transversales a lo largo del coche, así que el techo, la cintura y los hombros salen redondeados. La pintura es metálica con barniz, con reflejos de un entorno que se genera en el juego; los cristales son oscuros y brillantes, una moldura negra recorre la cintura y líneas oscuras marcan puertas, capó y maletero. Las texturas `texturas/coche-*.jpg` (pintura, rejilla, neumáticos, llantas y matrícula) se generaron con un script de Python (Pillow) para este proyecto.

Los rivales del circuito llevan coches de carreras y deportivos de Kenney Car Kit 3.1 (CC0): `race`, `race-future`, `sedan-sports` y `hatchback-sports`.

El SUV compacto (`modelos/coches/suv-compacto.glb`) lo ha aportado el autor del juego. Es el modelo más detallado (58 500 triángulos), así que solo lo conduce el jugador, no sale en el tráfico. Sus propios pilotos LED se encienden al frenar y sus faros LED de noche.

La galería son ocho turismos de Kenney Car Kit, © Kenney (kenney.nl), licencia CC0 1.0: Berlina, Hatchback, Deportivo, SUV, SUV de lujo, Taxi, Patrulla y Furgoneta. Están en `modelos/coches/`, con sus datos en `catalogo.json`. Miden 4,2 m de largo; los que son más anchos o más altos de lo normal (sedán, taxi, deportivo, SUV) se estrechan o se bajan al cargarlos, sin agrandar ninguno, y el coche básico tiene silueta de coche, no una caja. El frontal mira a +Z, que es el sentido en el que avanza el juego.

CarConcept y ToyCar vienen de los modelos de ejemplo de Khronos glTF y están optimizados:

- CarConcept, © Eric Chadwick (Darmstadt Graphics Group), licencia CC BY 4.0.
- ToyCar, © Guido Odendahl y Eric Chadwick, licencia CC0.

En el tráfico de la carrera circulan los coches de la galería y versiones ligeras de CarConcept y ToyCar (`modelos/trafico-*.glb`, con un 10 % de los triángulos), hechas con Blender a partir de los originales.

`vendor/GLTFLoader.js` es el cargador de GLTF de three.js r128 (licencia MIT).

## Árboles, mar, piscinas y parques infantiles

**De la foto aérea** (la ortofoto que trae la zona) se sacan:

- **Árboles**, uno donde se ve cada copa: verde y oscura, con más verde alrededor, así que no cuenta una mata de hierba. Hay al menos 4,5 m entre árbol y árbol, y ninguno encima de casas, calzadas o agua. Las copas muy oscuras dentro de una masa de árboles (un pinar) son **pinos piñoneros**: tronco alto y algo torcido y copa ancha y plana. En Huelva salen unos 22.000.
- **El mar**: zona azulada a menos de 1,5 m de cota (la cota sale de `alturas.asc`), unida al borde de la foto y de más de 5 ha. Se dibuja con el agua del juego y choca. En Huelva sale un trozo de la ría del Odiel que el mapa no tenía.
- **Piscinas**: manchas turquesa de 6 a 150 m² que no estén ya en OpenStreetMap, con un borde de piedra clara.

**Sin foto**, los árboles salen del mapa: parques, praderas y ahora también **bosques** (con pinos piñoneros), bosques de frondosas y **matorral**. Antes el juego no miraba los bosques, por eso en zonas como Mazagón faltaban los pinos. El arenal y otras capas tienen ya su color de suelo.

**Parques infantiles**: los de OpenStreetMap (el Paseo los pide ahora: `leisure=playground`) y, en cada parque de 2.000 m² o más que no tenga, uno puesto por el juego. Llevan:

- suelo de caucho y una valla baja verde con entrada;
- columpios, tobogán con torre, balancín y dos muelles;
- dos bancos fuera.

Todo choca. En Huelva salen 60.

Los árboles son **robles, fresnos, pinos y arbustos generados con [ez-tree](https://github.com/dgreenheck/ez-tree)** (Daniel Greenheck, licencia MIT; `vendor/ez-tree.js`), con la corteza en PBR (color, normales y rugosidad de [Poly Haven](https://polyhaven.com)) y las hojas de [TextureCan](https://www.texturecan.com), las dos con licencia CC0 (`texturas/arboles/`). Cada especie tiene dos variantes, con el tamaño, el giro y el verde de cada árbol cambiados, y las hojas se mecen con el viento.

Se reparten según la distancia al coche, árbol a árbol: los 160 más cercanos (80 en el móvil) llevan el árbol completo en versión ligera, de 3.000 a 5.500 triángulos cada uno; el resto, una silueta de 16 triángulos, con el tronco y tres tarjetas cruzadas con las hojas. Así el coste no crece con lo poblado que esté un barrio. Antes eran modelos de Kenney de 70 a 230 triángulos, que ya no se usan (siguen en `modelos/arboles/`).

Los peatones y el público de las gradas son figuras humanas hechas en el propio juego (cabeza, torso, brazos y piernas con proporciones reales, con pelo, ojos y ropa de colores al azar). Caminan con el paso animado en el shader, y se paran cuando están quietos. Las licencias de cada paquete van junto a los modelos y las texturas.

Cada peatón tiene edad, personalidad y, a veces, ocupación, y una ira de 0 a 100. El claxon (`H`), un coche parado a su lado o uno que pasa rozándole la suben; se calma cuando el coche se aleja. Según el carácter reacciona de una manera: el niño y el tímido huyen, el anciano huye despacio, el pasivo y el distraído se quedan quietos, el agresivo, el joven y el protector se enfrentan mirando al coche, el adolescente graba, el curioso saca una foto, y el agente de policía no se mueve y multa. Con ira alta gritan (con un tono según la edad), y algunos golpean el coche o le tiran objetos. Los peatones no llevan accesorios (móvil, cochecito, bastón…) porque el juego no tiene esos modelos: su carácter se nota en el movimiento, el color y el sonido.

## Datos

`zona-huelva.zip` incluye datos de OpenStreetMap (© colaboradores de OpenStreetMap, licencia ODbL) y del Catastro (Dirección General del Catastro). El juego usa `calles.geojson` y `casas.geojson`, `mapa.geojson` (usos del suelo: parques, praderas, cultivos, agua), `datos/osm.json` (piscinas y rotondas) y `ortofoto.jpg` con `ortofoto.jgw` (foto aérea del suelo). Si faltan, la zona carga igual, sin ese paisaje.

La ortofoto es PNOA, © Instituto Geográfico Nacional de España, licencia CC BY 4.0.
