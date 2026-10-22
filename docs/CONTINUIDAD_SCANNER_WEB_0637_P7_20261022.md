# Scanner Web 0.6.37 P7 — continuación
Fecha: 2026-10-22

**Nota de alcance:** este repositorio todavía conserva principalmente el complemento de perfil Web → Mobile. El código PHP completo de la versión P7 fue empaquetado como ZIP para pruebas; **el árbol de fuentes íntegro todavía no está publicado aquí**. No desplegar este repositorio como si fuese la aplicación completa.

## Cambios implementados en el paquete local P7
- Módulo independiente en Inicio: **Documentos procesados**. Permite consultar documentos de varios tipos, filtrarlos y marcar/restaurar en lote, sin afectar documentos Profit. Autorización: `documents.archive`.
- Picking reorganizado como opciones visuales **Por número / Por cliente / Disponibles**; la sesión multi-almacén muestra cantidades agrupadas y almacén activo.
- Devolución parcial de cliente: elegir renglones de la factura o nota original, ingresar cantidad devuelta por renglón, validar máximos y cambios en la fuente, y escanear solo artículos incluidos. Mantiene la referencia al documento original.
- Se conserva la recepción por ajustes de producción del almacén 02, XML y procesos anteriores.

## Pruebas locales
- 78 archivos PHP con sintaxis válida.
- Casos de cantidades jerárquicas, ajuste de producción, XML y selección parcial: PASS.

## Pendientes
- Publicar **todos** los fuentes P7 en este repositorio, conservando manifiesto e historial.
- Validar con la base Profit y la base local de Scanner de VIMAR.
- No hay APK nueva ni importador XML Profit en esta entrega.

**Advertencia:** la devolución acumulada aún no reconcilia devoluciones previas registradas en Profit; su control corresponderá al futuro importador.