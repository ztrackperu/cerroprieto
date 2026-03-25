/**
 * Exportación Excel (.xlsx), CSV y PDF desde tarjetas de módulos (Starcool, Atmósfera, Madurador).
 * Requiere: SheetJS (XLSX), jsPDF + jspdf-autotable (cargados en AdminPage).
 */
(function () {
    'use strict';

    function sanitizeFilename(base) {
        var s = String(base || 'export').replace(/[^a-zA-Z0-9_-]/g, '_');
        return s || 'export';
    }

    function timestampSuffix() {
        var d = new Date();
        var pad = function (n) {
            return String(n).padStart(2, '0');
        };
        return (
            d.getFullYear() +
            '-' +
            pad(d.getMonth() + 1) +
            '-' +
            pad(d.getDate()) +
            '_' +
            pad(d.getHours()) +
            pad(d.getMinutes()) +
            pad(d.getSeconds())
        );
    }

    function parseRowsFromScript(el) {
        if (!el || !el.textContent) {
            return [];
        }
        try {
            var data = JSON.parse(el.textContent.trim());
            return Array.isArray(data) ? data : [];
        } catch (e) {
            console.error('exportModulosDatos: JSON inválido', e);
            return [];
        }
    }

    function toMatrix(rows) {
        var m = [['Parámetro', 'Valor']];
        rows.forEach(function (r) {
            m.push([r.label != null ? String(r.label) : '', r.value != null ? String(r.value) : '']);
        });
        return m;
    }

    function downloadCsv(rows, fileBase) {
        var matrix = toMatrix(rows);
        var csv = matrix
            .map(function (row) {
                return row.map(function (cell) {
                    var s = String(cell != null ? cell : '');
                    if (/[",\n\r]/.test(s)) {
                        return '"' + s.replace(/"/g, '""') + '"';
                    }
                    return s;
                }).join(',');
            })
            .join('\r\n');
        var blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = sanitizeFilename(fileBase) + '_' + timestampSuffix() + '.csv';
        a.click();
        setTimeout(function () {
            URL.revokeObjectURL(a.href);
        }, 0);
    }

    function downloadXlsx(rows, fileBase) {
        if (typeof XLSX === 'undefined') {
            window.alert('No se pudo cargar la librería Excel. Recargue la página.');
            return;
        }
        var matrix = toMatrix(rows);
        var ws = XLSX.utils.aoa_to_sheet(matrix);
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Datos');
        XLSX.writeFile(wb, sanitizeFilename(fileBase) + '_' + timestampSuffix() + '.xlsx');
    }

    function downloadPdf(rows, title, fileBase) {
        var jspdf = window.jspdf;
        if (!jspdf || typeof jspdf.jsPDF !== 'function') {
            window.alert('No se pudo cargar la librería PDF. Recargue la página.');
            return;
        }
        var Doc = jspdf.jsPDF;
        var doc = new Doc({ orientation: 'p', unit: 'mm', format: 'a4' });
        var t = title || 'Datos';
        doc.setFontSize(12);
        doc.text(t, 14, 16);
        var body = rows.map(function (r) {
            return [r.label != null ? String(r.label) : '', r.value != null ? String(r.value) : ''];
        });
        if (typeof doc.autoTable === 'function') {
            doc.autoTable({
                startY: 22,
                head: [['Parámetro', 'Valor']],
                body: body,
                styles: { fontSize: 9, cellPadding: 2 },
                headStyles: { fillColor: [13, 110, 253] },
                margin: { left: 14, right: 14 }
            });
        } else {
            var y = 30;
            body.forEach(function (row) {
                doc.setFontSize(9);
                doc.text(row[0] + ': ' + row[1], 14, y);
                y += 6;
            });
        }
        doc.save(sanitizeFilename(fileBase) + '_' + timestampSuffix() + '.pdf');
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-export-modulo-datos');
        if (!btn) {
            return;
        }
        e.preventDefault();
        var id = btn.getAttribute('data-export-target');
        if (!id) {
            return;
        }
        var scriptEl = document.getElementById(id);
        var rows = parseRowsFromScript(scriptEl);
        if (!rows.length) {
            window.alert('No hay datos para exportar en este módulo.');
            return;
        }
        var title = btn.getAttribute('data-export-title') || 'Exportación';
        var fileBase = btn.getAttribute('data-export-file') || 'datos';
        var fmt = (btn.getAttribute('data-export-format') || 'csv').toLowerCase();
        if (fmt === 'csv') {
            downloadCsv(rows, fileBase);
        } else if (fmt === 'xlsx') {
            downloadXlsx(rows, fileBase);
        } else if (fmt === 'pdf') {
            downloadPdf(rows, title, fileBase);
        }
    });
})();
