# Scanner Web 0.6.36 - Addon Perfil Mobile CFGPORT1

Este paquete NO sustituye el Scanner Web completo. Se copia encima de una instalación existente Scanner Web 0.6.36.

## Instalación

Copiar `config_profile_mobile.php` en la raíz actual de Scanner, por ejemplo:

`C:\xampp\htdocs\scanner\config_profile_mobile.php`

No reemplaza archivos existentes ni modifica la base de datos.

## Uso

1. Ingresar normalmente a Scanner Web.
2. Abrir:
   `http://SERVIDOR/scanner/config_profile_mobile.php`
3. Escribir empresa/base Profit, por ejemplo VIMAR.
4. Pulsar **Analizar configuración**.
5. Verificar las secciones detectadas.
6. Pulsar **Generar JSON para APK**.
7. Pasar el JSON al teléfono.
8. En Scanner Mobile importar el perfil antes del primer login de esa empresa.
9. Configurar la URL LAN del Scanner Bridge y pulsar **Preparar desde Profit**.

## Seguridad

El exportador excluye credenciales, password, secret, token, DSN, servidor/base SQL, rutas locales/UNC, carpetas de intercambio y URL del Bridge.

## Alcance

El JSON conserva una instantánea sanitizada de las pantallas de configuración que estén disponibles en la versión Web. La sincronización de datos vivos (artículos, barcodes, stock, documentos, usuarios e imágenes) sigue siendo responsabilidad de Scanner Bridge.
