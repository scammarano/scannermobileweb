# Prueba Vimar — Scanner Web + Scanner Mobile

## 1. En el servidor Vimar

Copiar `config_profile_mobile.php` en la raíz de Scanner Web.

Ejemplo:

`C:\xampp\htdocs\scanner\config_profile_mobile.php`

No reemplaza archivos existentes y no modifica la base Profit.

## 2. Generar el perfil

1. Abrir Scanner Web e iniciar sesión.
2. Abrir:
   `http://SERVIDOR/scanner/config_profile_mobile.php`
3. Indicar el código/base real de la empresa Vimar.
4. Pulsar **Analizar configuración**.
5. Verificar las secciones detectadas.
6. Pulsar **Generar JSON para APK**.

Se descargará un archivo similar a:

`SCANNER_WEB_PROFILE_VIMAR_WEB0.6.36.json`

## 3. En el Android

1. Instalar Scanner Mobile 0.3.4 o superior.
2. Antes del primer login, seleccionar **Importar perfil Web JSON**.
3. Seleccionar el JSON generado en Vimar.
4. Indicar la URL LAN del Scanner Bridge.
5. Pulsar **Preparar desde Profit**.
6. Confirmar que descarga usuarios, artículos, códigos, presentaciones, stock, documentos e imágenes.
7. Iniciar sesión con un usuario real sincronizado desde Vimar.

## 4. Prueba offline

Después de completar la primera sincronización:

1. Desconectar Wi-Fi/datos.
2. Consultar artículos previamente sincronizados.
3. Abrir documentos previamente descargados.
4. Realizar una operación de prueba.
5. Confirmar que queda en cola local.
6. Reconectar la red.
7. Ejecutar sincronización.
8. Confirmar que la operación pasa al Bridge y luego a Profit según el flujo configurado.

## 5. Qué NO debe copiar el perfil

No importar entre instalaciones:

- contraseña SQL;
- usuario SQL;
- token/secret;
- DSN;
- servidor SQL;
- rutas `R:\` o UNC;
- carpetas OUTBOX/PENDING/PROCESSED/ERROR;
- URL particular del Scanner Bridge.

Esos valores pertenecen a cada instalación.
