// Durum sınıflarını tanımlayan fonksiyon
function getStatusClass(status) {
    switch (status) {
        case "Yeni Gelen":
            return "bg-red-500";
        case "Geri Gelen":
            return "bg-yellow-500";
        case "Çıkış Yapıldı":
            return "bg-orange-500";
        case "Tamir Edildi":
            return "bg-green-500";
        case "Tamir Edilmedi":
            return "bg-blue-500";
        default:
            return "bg-gray-400";
    }
}

// Modal (pop-up) oluşturma fonksiyonu
function createModal(title, content) {
    const modal = document.createElement('div');
    modal.classList.add('modal-backdrop');
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">${title}</h5>
                <button type="button" class="close-modal-button">&times;</button>
            </div>
            <div class="modal-body">
                ${content}
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    // Kapatma butonu için olay dinleyicisi
    modal.querySelector('.close-modal-button').addEventListener('click', () => {
        modal.remove();
    });

    // Pop-up dışına tıklama olayı
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });

    return modal.querySelector('.modal-body');
}

// Tabulator tablosunu oluşturma
var table = new Tabulator("#envanter-tablosu", {
    layout: "fitColumns",
    
    rowFormatter: function(row) {
        var data = row.getData();
        var detailsElement = document.createElement("div");
        detailsElement.classList.add("detail-row");

        detailsElement.innerHTML = `
            <div class="detail-item"><strong>Seri No:</strong> ${data.seriNo}</div>
            <div class="detail-item"><strong>Giriş Tarihi:</strong> ${data.girisTarihi}</div>
            <div class="detail-item"><strong>İşlemler:</strong> 
                <button class="islemler-button teklif-pdf" data-id="${data.id}">Teklif PDF</button>
                <button class="islemler-button ayrintilar" data-id="${data.id}">Ayrıntılar</button>
            </div>
        `;

        row.getElement().appendChild(detailsElement);
        detailsElement.style.display = 'none';

        var teklifButton = detailsElement.querySelector(".teklif-pdf");
        var ayrintilarButton = detailsElement.querySelector(".ayrintilar");

        teklifButton.addEventListener("click", function(e) {
            e.stopPropagation();

            var rowData = row.getData();
            const proposalContent = `
                <div id="proposal-content">
                    <h2>Teklif PDF - Barkod: ${rowData.barkod}</h2>
                    <table>
                        <tr>
                            <td><strong>Cihaz Kategorisi:</strong></td>
                            <td>${rowData.cihazKategorisi}</td>
                        </tr>
                        <tr>
                            <td><strong>Cihaz Bilgisi:</strong></td>
                            <td>${rowData.cihazBilgisi}</td>
                        </tr>
                        <tr>
                            <td><strong>Seri No:</strong></td>
                            <td>${rowData.seriNo}</td>
                        </tr>
                        <tr>
                            <td><strong>Giriş Tarihi:</strong></td>
                            <td>${rowData.girisTarihi}</td>
                        </tr>
                        <tr>
                            <td><strong>Çıkış Durumu:</strong></td>
                            <td>${rowData.cikisDurumu}</td>
                        </tr>
                    </table>
                    <p>Bu teklif, yukarıda belirtilen cihaz için otomatik olarak oluşturulmuştur.</p>
                    <div class="pdf-download-section">
                        <button id="download-pdf-button" class="action-button">PDF Olarak İndir</button>
                    </div>
                </div>
            `;
            
            const modalBody = createModal(`Teklif - Barkod: ${rowData.barkod}`, proposalContent);

            modalBody.querySelector('#download-pdf-button').addEventListener('click', () => {
                const elementToPrint = modalBody.querySelector('#proposal-content');
                html2canvas(elementToPrint, {
                    scale: 2
                }).then(canvas => {
                    var imgData = canvas.toDataURL('image/jpeg', 1.0);
                    var pdf = new jspdf.jsPDF('p', 'mm', 'a4');
                    var imgWidth = 210;
                    var pageHeight = 295;
                    var imgHeight = canvas.height * imgWidth / canvas.width;
                    var heightLeft = imgHeight;
                    var position = 0;

                    pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;

                    while (heightLeft >= 0) {
                        position = heightLeft - imgHeight;
                        pdf.addPage();
                        pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight);
                        heightLeft -= pageHeight;
                    }

                    pdf.save(`Teklif_${rowData.barkod}.pdf`);
                });
            });
        });

        ayrintilarButton.addEventListener("click", function(e) {
            e.stopPropagation();
            alert("Ayrıntılar butonu tıklandı.");
        });

        row.getElement().addEventListener("click", function(e) {
            if (!e.target.classList.contains("islemler-button")) {
                if (detailsElement.style.display === 'none') {
                    detailsElement.style.display = 'block';
                } else {
                    detailsElement.style.display = 'none';
                }
                row.normalizeHeight();
            }
        });
    },
    columns: [
        {
            title: "Durum",
            field: "durum",
            hozAlign: "center",
            formatter: function(cell) {
                var value = cell.getValue();
                var statusClass = getStatusClass(value);
                return `<span class="${statusClass} text-white text-xs font-medium px-2 py-1 rounded-full">${value}</span>`;
            }
        },
        {
            title: "Süreç Durumu",
            field: "surecDurumu",
            hozAlign: "center",
            formatter: function(cell) {
                var value = cell.getValue();
                var statusClass = getStatusClass(value);
                return `<span class="${statusClass} text-black text-xs font-medium px-2 py-1 rounded-full">${value}</span>`;
            }
        },
        {
            title: "Cihaz Durumu",
            field: "cihazDurumu",
            hozAlign: "center",
        },
        {
            title: "Çıkış Durumu",
            field: "cikisDurumu",
            hozAlign: "center",
            formatter: function(cell) {
                var value = cell.getValue();
                var statusClass = getStatusClass(value);
                return `<span class="${statusClass} text-black text-xs font-medium px-2 py-1 rounded-full">${value}</span>`;
            }
        },
        {
            title: "Barkod",
            field: "barkod",
            hozAlign: "center",
        },
        {
            title: "Cihaz Kategorisi",
            field: "cihazKategorisi",
            hozAlign: "center",
        },
        {
            title: "Cihaz Bilgisi",
            field: "cihazBilgisi",
            hozAlign: "left",
        },
        {
            title: "Giriş Tarihi",
            field: "girisTarihi",
            hozAlign: "center",
        }
    ],
});

document.addEventListener('DOMContentLoaded', async function() {
    // Flatpickr kütüphanesini başlatma
    const dateRangePicker = flatpickr("#filter-dateRange", {
        mode: "range",
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            applyFilters();
        }
    });

    try {
        const response = await fetch('http://localhost:8080/api/envanter/listele');
        if (!response.ok) {
            throw new Error(`HTTP hatası! Durum: ${response.status}`);
        }
        const data = await response.json();
        table.setData(data);
    } catch (err) {
        console.error("Veri çekme hatası:", err);
    }

    var excelButton = document.getElementById("excel-export-button");
    if (excelButton) {
        excelButton.addEventListener("click", function() {
            table.download("xlsx", "data.xlsx", {
                sheetName: "Envanter Verileri"
            });
        });
    }

    const filterDurum = document.getElementById('filter-durum');
    const filterSurecDurumu = document.getElementById('filter-surecDurumu');
    const filterCihazDurumu = document.getElementById('filter-cihazDurumu');
    const filterCikisDurumu = document.getElementById('filter-cikisDurumu');
    const filterBarkod = document.getElementById('filter-barkod');
    const filterCihazBilgisi = document.getElementById('filter-cihazBilgisi');
    const filterCihazKategorisi = document.getElementById('filter-cihazKategorisi');
    const clearFiltersButton = document.getElementById('clear-filters-button');

    function applyFilters() {
        const filters = [];
        if (filterDurum.value) {
            filters.push({ field: "durum", type: "like", value: filterDurum.value });
        }
        if (filterSurecDurumu.value) {
            filters.push({ field: "surecDurumu", type: "like", value: filterSurecDurumu.value });
        }
        if (filterCihazDurumu.value) {
            filters.push({ field: "cihazDurumu", type: "like", value: filterCihazDurumu.value });
        }
        if (filterCikisDurumu.value) {
            filters.push({ field: "cikisDurumu", type: "like", value: filterCikisDurumu.value });
        }
        if (filterBarkod.value) {
            filters.push({ field: "barkod", type: "like", value: filterBarkod.value });
        }
        if (filterCihazBilgisi.value) {
            filters.push({ field: "cihazBilgisi", type: "like", value: filterCihazBilgisi.value });
        }
        if (filterCihazKategorisi.value) {
            filters.push({ field: "cihazKategorisi", type: "=", value: filterCihazKategorisi.value });
        }

        const selectedDates = dateRangePicker.selectedDates;
        if (selectedDates.length === 2) {
            const startDate = flatpickr.formatDate(selectedDates[0], "Y-m-d");
            const endDate = flatpickr.formatDate(selectedDates[1], "Y-m-d");
            
            filters.push({ field: "girisTarihi", type: ">=", value: startDate });
            filters.push({ field: "girisTarihi", type: "<=", value: endDate });
        }

        table.setFilter(filters);
    }

    filterDurum.addEventListener('input', applyFilters);
    filterSurecDurumu.addEventListener('input', applyFilters);
    filterCihazDurumu.addEventListener('input', applyFilters);
    filterCikisDurumu.addEventListener('input', applyFilters);
    filterBarkod.addEventListener('input', applyFilters);
    filterCihazBilgisi.addEventListener('input', applyFilters);
    filterCihazKategorisi.addEventListener('change', applyFilters);

    clearFiltersButton.addEventListener('click', function() {
        filterDurum.value = '';
        filterSurecDurumu.value = '';
        filterCihazDurumu.value = '';
        filterCikisDurumu.value = '';
        filterBarkod.value = '';
        filterCihazBilgisi.value = '';
        filterCihazKategorisi.value = '';
        dateRangePicker.clear();
        table.clearFilter();
    });
});