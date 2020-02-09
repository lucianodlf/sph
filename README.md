### Separate Hours

**NOTA: Para cualquiera que de con este proyecto, esta explicacion esta
realizada teniendo en cuenta usuarios no experimentados con la terminal.**

**Melina y Maxi, con cariño me refiero a ustedes. Aunque admito desconocer
si realmente lo que van a ver aqui, es totalmente nuevo para ustedes**

**El contexto es de prueba para la implementacion de esta aplicacion en estado
beta sobre plataforma Windows. Algo mas largo de explicar el por que no puede
ser verificado adecuadamente en estos momento cuando escribo el texto...**

**En fin, fuiste advertida/o ;)**

**Los errores de ortografia son seyo personal ;)**

#### Como instalamos en entorno Windows (Paso a paso) (Temporal)

##### Primero necesitamos Git

Nos descargamos el instalador de git (asumiento que es Windows 7 o superior de 64 bits)
[text](https://github.com/git-for-windows/git/releases/download/v2.25.0.windows.1/Git-2.25.0-64-bit.exe)

Ejecutamos el instalador.

__NOTA:__ Tener en cuenta en la pantalla con titulo _"Select Components"_ Solo seleccionar las opciones:
* Git Bash  Here
* Associate .git* configurations files whit the default text editor
* Associate .sh files to be run with Bash

El resto de las opciones dejarlas configuradas como estan.
En el menu inicio deberiamos tener un acceso directo a __Git Bash__

__NOTA:__ Si no tenemos el acceso directo, nos animamos a creer que Git se instalo
en _C:\Program Files\Git_ por lo que podemos ir a esa ubicacion y utilizar el 
acceso llamado __git-bash__

De alguna forma tenemos que tener habierto Git bash y deberiamos ver una
pantalla negra (generalmente) con un texto similar a este:

```
Usuario@DESKTOP-GTBI7E8 MINGW64
$
```

Esto se suele llamar "el terminal" o "la consola"
Si llegamos a este punto, estamos bien! Felicitaciones. podemos continuar...

##### Ahora tenemos que preparar el entorno

En los ejemplo, el signo __$__ no se debe escribir, el terminal lo coloca siempre.
algo llamado _prompt_
Tambien, **si hay texto con un __#__ (numeral), son comentarios, no se deben
escribir.**

```
# Con esto creamos un directorio en C:/ llamado 'sph'
$ mkdir /c/sph

# Nos movemos al directorio creado
$ cd /c/sph/

# Deberiamos ver algo parecido a esto: Usuario@DESKTOP-GTBI7E8 MINGW64 /c/sph
```

Ahora tenemos que clonar el proyecto
```
# Clonamos con el comando git clone ...
$ git clone https://gitlab.com/lucianoldf/sph.git

# Tendriamos que ver algo asi, mientras esperamos que se decargue
Cloning into 'sph'...
remote: Enumerating objects: 121, done.
remote: Counting objects: 100% (121/121), done.
remote: Compressing objects: 100% (107/107), done.
remote: Total 121 (delta 5), reused 121 (delta 5), pack-reused 0
Receiving objects: 100% (121/121), 25.69 MiB | 322.00 KiB/s, done.
Resolving deltas: 100% (5/5), done.
Updating files: 100% (107/107), done.
```

Si todo salio bien, se nos tendria que creear otro directorio llamado
__sph__ por lo que nos movemos a ese directorio
```
$ cd sph/

# Luego podemos usar el comando pwd para ver si estamos en la ruta correcta
$ pwd

# Deveriamos ver algo asi:
/c/sph/sph

Y nuestro prompt deberia ser algo parecido a esto
Usuario@DESKTOP-GTBI7E8 MINGW64 /c/sph/sph (master)
```

Si llegamos bien a este punto, vamos a user un pequeño scrip sh para 
preparar el entorno
```
$ ./install.sh
listo ;)
```

En este punto, si viste un mensaje como el anterior al ejecutar _./install.sh_
podemos creer que todo esta bien y lo que vamos a hacer es cerrar Git Bash (el terminal)
y volverlo a abrir.
Luego de abrirlo vamos a hacer lo siguiente:

```
# Escrivimosd php -v para ver si se reconoce el comando php.
# Es importante que veas el texto como se muestra en el ejemplo.
$ php -v
PHP 7.3.14 (cli) (built: Jan 21 2020 13:15:39) ( NTS MSVC15 (Visual C++ 2017) x64 )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.3.14, Copyright (c) 1998-2018 Zend Technologies

# Luego nos movemos al proyecto que clonamos en /c/sph/sph
$ cd /c/sph/sph/

# Tnemos que ver si se reconoce composer, con este comando:
$ composer.phar -V
Composer version 1.9.3 2020-02-04 12:58:49

# Es importante que veamos esa ultima linea que indica la vevrsion.
```

En este punto, si todo esta bien (deseo que si), ya tenemos una terminal de
Git Bash, con php y composer. Necesitamos instalar las dependencias con
composer

```
# Vamos a usar el comando 'composer.phar install'
# Puede que demore un poco, pero tendriamos que ver como se muestra en el ejemplo.

$ composer.phar install
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 5 installs, 0 updates, 0 removals
  - Installing psr/simple-cache (1.0.1): Downloading (100%)
  - Installing markbaker/matrix (1.2.0): Downloading (100%)
  - Installing markbaker/complex (1.4.7): Downloading (100%)
  - Installing phpoffice/phpspreadsheet (1.10.1): Downloading (100%)
  - Installing docopt/docopt (1.0.4): Downloading (100%)
phpoffice/phpspreadsheet suggests installing mpdf/mpdf (Option for rendering PDF with PDF Writer)
phpoffice/phpspreadsheet suggests installing dompdf/dompdf (Option for rendering PDF with PDF Writer)
phpoffice/phpspreadsheet suggests installing tecnickcom/tcpdf (Option for rendering PDF with PDF Writer)
phpoffice/phpspreadsheet suggests installing jpgraph/jpgraph (Option for rendering charts, or including charts with PDF or HTML Writers)
Writing lock file
Generating autoload files

```

Ya casi estamos...

##### Ahora solo nos queda probar que se ejecute bien el script php.

Para probarlo, primero vamos a ejecutar el siguiente comando

```
$php sph.php --version
1.0.0


# Si pudimos ver la version, casi parace que se puede ejecutar.
# Hay una ayuda extendida de las posibilidades de uso. que se puede ver con
# php sph.php -h

$ php sph.php -h

Usage:
        sph.php [-h | --help]
        sph.php [--quiet | --verbose] [-x] [--o-file=FILE] [INPUT]
        sph.php [-f FILE | --cfg=FILE]
        sph.php [--verbose] [--not-export-excel] [--i-file=FILE | --i-dir-path=DIR] [--o-file=FILE | --o-dir-path=DIR]
                [--date-format-read=FORMAT] [--date-format-show=FORMAT]
                [--range-read=RANGE] [--sort-order=ORDER]
        sph.php --debug-mode [--d-user-only=VALUE | --d-max-users=VALUE] [--d-interval-only=VALUE | --d-max-intervals=VALUE]
        [--d-hidde-log-by-minute] [--d-range-datetime=VALUE]
        [--not-export-excel] [--i-file=FILE | --i-dir-path=DIR] [--o-file=FILE | --o-dir-path=DIR]
        [--date-format-read=FORMAT] [--date-format-show=FORMAT]
        [--range-read=RANGE] [--sort-order=ORDER] [--verbose]
        sph.php --version

Example:
        php sph.php --i-file=./import/planilla_horas_base_2.xlsx
        php sph.php --i-file=./import/planilla_input_test_2q_septimebre_2019.xlsx --o-file=./testeo.xlsx


Options:
-h --help                       show this
-q --quiet                      print less text
-v --verbose                    print more text
-d --debug-mode                 Enable mode debug
-x --not-export-excel           Disable export result to excel file (xlsx) [default: TRUE]
-f FILE --cfg=FILE              specify read configu file (DISABLE)
--version


[File]
--i-file=FILE                   specify output file export
--o-file=FILE                   specify input file import
--i-dir-path=DIR                specify input dir path [default: ./import]
--o-dir-path=DIR                specify input dir path [default: ./export]


[Format date]
--date-format-read=FORMAT       specify format date for read [default: 'm/d/Y H:i']
--date-format-show=FORMAT       specify format date for show [default: 'd/m/Y H:i']
--range-read=VALUE              specify range read of input file [default: 'A1:D10000']
--sort-order=VALUE              specify order sorter of input file (0 = deshabilitado, 1 = ascendente, 2 = descendente) [default: 0]


[Debug] Reuire --debug-mode = TURE
--d-user-only=VALUE             process only user code [default: 0]
--d-max-users=VALUE             process a max number of users [default: 0]
--d-interval-only=VALUE         process only one interval. EXAMPLE: "INTERVAL_0"
--d-max-intervals=VALUE         process max num intervals by user [default: 0]. Example: "INTERVAL_1,INTERVAL_2"
--d-hidde-log-by-minute         hidde log by minute [default: FALSE]
--d-range-datetime=VALUE                proces only range date/time. FORMAT EXAMPLE: "06/09/2019 00:00,06/09/2019 09:15"
```

Ahora para probar si se ejecuta el script, podemos hacerlo solo con alguno
de los arhcivos excel de importacion que estan de ejemplo, para eso
con que usemos el comando _php sph.php_ sin ninguna otra opcion es suficiente.

**NOTA: Si se usa sin opciones, el script busca en el directorio _./import/_
el archivo .xlsx mas nuevo, e intenta importar ese archivo.**

Ejemplo:
```
$ php sph.php
2020-02-09 06:35:45: Start script
2020-02-09 06:35:45: Import file: C:\sph\sph/import/planilla_horas_base_2.xlsx
2020-02-09 06:35:45: Import file: C:\sph\sph/import/planilla_horas_base_2.xlsx
2020-02-09 06:35:45: Process ...


| CU |    NOMBRE     | INTERVALO |     INGRESO      |      EGRESO      | DIURNAS  | DIURNAS (E) |NOCTURNAS |NOCTURNAS (E)|H100 |FERIADO|ALERTA|TOTAL|FIXED|
------------------------------------------------------------------------------------------------------------------------------------------------------------
| 57 |ALBRECHT DAMIA |INTERVAL_0 | 02/09/2019 06:00 | 02/09/2019 15:00 |  09:00   |             |          |             |     |  0;0  |      |09:00|     |
| 57 |ALBRECHT DAMIA |INTERVAL_1 | 03/09/2019 06:00 | 03/09/2019 16:00 |  09:00   |    01:00    |          |             |     |  0;0  |      |10:00|     |
| 57 |ALBRECHT DAMIA |INTERVAL_2 | 04/09/2019 06:00 | 04/09/2019 15:15 |  09:00   |    00:15    |          |             |     |  0;0  |      |09:15|     |
| 57 |ALBRECHT DAMIA |INTERVAL_3 | 05/09/2019 06:00 | 05/09/2019 15:15 |  09:00   |    00:15    |          |             |     |  0;0  |      |09:15|     |
| 57 |ALBRECHT DAMIA |INTERVAL_4 | 06/09/2019 06:00 | 06/09/2019 15:00 |  08:00   |    01:00    |          |             |     |  0;0  |      |09:00|     |
| 57 |ALBRECHT DAMIA |INTERVAL_5 | 09/09/2019 06:00 | 09/09/2019 15:30 |  09:00   |    00:30    |          |             |     |  0;0  |      |09:30|     |
| 57 |ALBRECHT DAMIA |INTERVAL_6 | 10/09/2019 06:00 | 10/09/2019 15:15 |  09:00   |    00:15    |          |             |     |  0;0  |      |09:15|     |
| 57 |ALBRECHT DAMIA |INTERVAL_7 | 11/09/2019 06:00 | 11/09/2019 15:30 |  09:00   |    00:30    |          |             |     |  0;0  |      |09:30|     |
| 57 |ALBRECHT DAMIA |INTERVAL_8 | 12/09/2019 06:00 | 12/09/2019 15:00 |  09:00   |             |          |             |     |  0;0  |      |09:00|     |
| 57 |ALBRECHT DAMIA |INTERVAL_9 | 13/09/2019 06:00 | 13/09/2019 15:00 |  08:00   |    01:00    |          |             |     |  0;0  |      |09:00|     |
| 62 | ALVAREZ JULIO |INTERVAL_0 | 03/09/2019 00:00 | 03/09/2019 09:15 |  03:00   |    00:15    |  06:00   |             |     |  0;0  |      |09:15|  X  |
| 62 | ALVAREZ JULIO |INTERVAL_1 | 04/09/2019 00:00 | 04/09/2019 09:15 |  03:00   |    00:15    |  06:00   |             |     |  0;0  |      |09:15|  X  |
| 62 | ALVAREZ JULIO |INTERVAL_2 | 05/09/2019 00:00 | 05/09/2019 09:00 |  03:00   |             |  06:00   |             |     |  0;0  |      |09:00|  X  |
| 62 | ALVAREZ JULIO |INTERVAL_3 | 06/09/2019 00:00 | 06/09/2019 09:15 |  02:00   |    01:15    |  06:00   |             |     |  0;0  |      |09:15|  X  |
| 62 | ALVAREZ JULIO |INTERVAL_4 | 06/09/2019 22:30 | 07/09/2019 07:00 |  00:30   |    00:30    |  07:30   |             |     |  0;0  |      |08:30|     |
| 62 | ALVAREZ JULIO |INTERVAL_5 | 09/09/2019 23:30 | 10/09/2019 09:30 |  02:30   |    01:00    |  06:30   |             |     |  0;0  |      |10:00|     |
| 62 | ALVAREZ JULIO |INTERVAL_6 | 10/09/2019 23:30 | 11/09/2019 09:00 |  02:30   |    00:30    |  06:30   |             |     |  0;0  |      |09:30|     |
| 62 | ALVAREZ JULIO |INTERVAL_7 | 12/09/2019 00:00 | 12/09/2019 09:15 |  03:00   |    00:15    |  06:00   |             |     |  0;0  |      |09:15|  X  |
| 62 | ALVAREZ JULIO |INTERVAL_8 | 12/09/2019 23:30 | 13/09/2019 09:00 |  02:30   |    00:30    |  06:30   |             |     |  0;0  |  X   |09:30|     |
| 62 | ALVAREZ JULIO |INTERVAL_9 | 13/09/2019 22:30 | 14/09/2019 07:00 |  00:30   |    00:30    |  07:30   |             |     |  0;0  |      |08:30|     |


2020-02-09 06:35:45: Export result file: C:\sph\sph/export/20200209/20200209063545_export.xlsx
2020-02-09 06:35:45: End script ;)

```

Por defecto, ademas de mostrar el resultado por pantall, tambien se exporta en archivo excel (xlsx)
en el directorio *./export/aqui-fecha-actual-en-formato-japones/..._export.xlsx*

Otra forma de uso, es indicando especificamente que arhcivo se desea importar:j
```
$ php sph.php --i-file=./import/planilla_horas_base_2.xlsx
```

O tambien indicando el archivo que se importa y que se exporta:
```
$ php sph.php --i-file=./import/planilla_input_test_2q_septimebre_2019.xlsx --o-file=./testeo.xlsx
```

Con esto ya tenemos una forma basica de probarlo en el entorno de windows

Luego intentare subir mas informacion sobre como usarlo, aunque para algunos
la ayuda de _-h_ puede ser suficiente.


---


**NOTA FINAL: Tener en cuenta que es importante que los arhcivos que se importen tenga el formato adecuado,
como el que esta en el direcotiro _./import/_ de ejemplo *"planilla_horas_base_2.xlsx*.**
**- No deben tener fila principal de titulo**
**- Deben estar las mismas columnas, sin datos adicionales**



