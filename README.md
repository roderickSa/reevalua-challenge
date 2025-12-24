# Generate Reports - Game01

Este módulo se encarga de generar reportes financieros (préstamos, deudas y tarjetas de crédito) en formato **XLSX** y comprimirlos en un **ZIP**, procesando grandes volúmenes de datos de manera eficiente usando *chunks* para no saturar la memoria.

---

## ⚙️ Instalación

1. **Clona o integra** el módulo en tu proyecto Laravel.
2. **Instala dependencias** si aún no lo has hecho:

```bash
composer install
```

3. **Configura la variable de entorno** para el tamaño del chunk en tu archivo .env:

REPORT_CHUNK_SIZE=100

4. **Ejecutar migraciones**:

```bash
php artisan migrate
```

5. **Ejecutar seeders**:

```bash
php artisan seed
```

6. **Levantar worker**:

```bash
php artisan queue:work
```

7. **Levantar Server**:

```bash
php artisan serve
```

## 🗂 Estructura Principal

### UseCase: `GenerateReportDateUseCase`
* Procesa los datos por chunks desde los repositorios (LoanRepository, CreditCardRepository, OtherDebtRepository).
* Mapea los registros a objetos `Report` usando `ReportMapper`.
* Exporta los datos a archivos XLSX en disco y los comprime en un ZIP.

### Repositorios:
* `LoanReportRepository`, `CreditCardReportRepository`, `OtherDebtReportRepository`.
* Implementan la interfaz `*RepositoryInterface`.
* Permiten consultar registros paginados por fecha mediante el método `findChunkByDate`.

### Exportador: `XlsxExporter`
* Implementa `XlsxExporterInterface`.
* Exporta arrays de `Report` a XLSX usando la librería **FastExcel**.

### Job: `GenerateReportJob`
* Permite ejecutar la generación de reportes en segundo plano usando colas (`ShouldQueue`).

### Controller: `GenerateReportController`
* Recibe la petición HTTP con el año y mes.
* Despacha el Job de generación de reportes.

## ⚡ Flujo de Ejecución

1. **Petición**: El cliente hace un request HTTP POST:

```bash
POST /api/generate-report
{
    "year": 2025,
    "month": 12
}
```

2. **Despacho**: El `GenerateReportController` envía el `GenerateReportJob` a la cola.

3. **Procesamiento**: El Job ejecuta `GenerateReportDateUseCase`:
* Consulta cada repositorio en bloques definidos por REPORT_CHUNK_SIZE.
* Mapea cada registro al objeto de dominio Report.
* Exporta el chunk a un archivo XLSX temporal.
* Limpia el buffer para liberar memoria después de cada iteración.

4. **Finalización**:
* Se crea un archivo ZIP con todos los XLSX generados.
* Se eliminan los archivos temporales.
* El archivo final queda disponible en: `storage/app/public/exports/reports/{YYYY-MM-DD}/report.zip`

## 💡 Características
* Escalable: Maneja miles o millones de registros sin saturar la memoria.
* Configuración flexible: Tamaño del chunk configurable vía `.env`.
* Optimización de memoria: Solo mantiene un chunk en memoria a la vez.
* Procesamiento en background: Uso de colas para no bloquear el servidor HTTP.
* Exportación a XLSX: Totalmente compatible con Excel, LibreOffice y Google Sheets.

## 📌 Dependencias Principales
* Laravel 10+
* FastExcel (rap2hpoutre/fast-excel)
* Queue Driver configurado (Database)

## 📝 Ejemplo de Generacion del zip

Request:

```bash
POST http://localhost:8000/api/generate-report \
     -H "Content-Type: application/json" \
     -d '{"year": 2025, "month": 12}'
```

Respuesta:

```bash
{
    "status": "processing"
}
```

**Ubicación del archivo**: Una vez finalizado, el archivo estará en: `/storage/exports/reports/2025-12-24/report.zip`
* Se genera con la fecha del dia cuando se creo el reporte

## 📝 Ejemplo de Consulta del zip generado

URL de descarga:

```bash
GET http://localhost:8000/api/reports/export?date=2025-12-24
```
