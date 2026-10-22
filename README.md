# Scanner Mobile Web

Repositorio de soporte para la versión Web de Scanner Core que se usa como referencia funcional del APK Scanner Mobile Standalone.

## Versión actual

- Scanner Web de referencia: **0.6.36**
- Add-on de exportación Web → Mobile: **CFGPORT1**
- Formato portable: **JSON**
- Compatible con Scanner Mobile **0.3.4+**

## Objetivo

La versión Web sigue siendo la referencia funcional. Este repositorio contiene el add-on que exporta la configuración portable de una instalación Web para cargarla luego en el APK.

## Instalación rápida

1. Descargue este repositorio como ZIP.
2. Extraiga `config_profile_mobile.php`.
3. Cópielo en la raíz de la instalación Scanner Web existente, por ejemplo:
   `C:\xampp\htdocs\scanner\config_profile_mobile.php`
4. Inicie sesión normalmente en Scanner Web.
5. Abra:
   `http://SERVIDOR/scanner/config_profile_mobile.php`
6. Indique la empresa/base Profit.
7. Pulse **Analizar configuración**.
8. Pulse **Generar JSON para APK**.
9. Pase el JSON al teléfono.
10. En Scanner Mobile use **Importar perfil Web JSON**.
11. Configure Scanner Bridge y ejecute **Preparar desde Profit**.

## Seguridad

El perfil portable NO debe incluir contraseñas SQL, tokens, secretos, DSN, rutas locales/UNC, carpetas OUTBOX/PENDING/PROCESSED/ERROR ni la URL propia del Bridge.

## Descarga directa

El ZIP del repositorio siempre se puede descargar desde:

`https://github.com/scammarano/scannermobileweb/archive/refs/heads/main.zip`
