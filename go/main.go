package main

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"strconv"

	_ "github.com/denisenkom/go-mssqldb"
	_ "github.com/mattn/go-sqlite3"
)

type EnvanterItem struct {
	Id              int    `json:"id"`
	Durum           string `json:"durum"`
	SurecDurumu     string `json:"surecDurumu"`
	CihazDurumu     string `json:"cihazDurumu"`
	CikisDurumu     string `json:"cikisDurumu"`
	Barkod          string `json:"barkod"`
	CihazKategorisi string `json:"cihazKategorisi"`
	CihazBilgisi    string `json:"cihazBilgisi"`
	SeriNo          string `json:"seriNo"`
	GirisTarihi     string `json:"girisTarihi"`
}

var validCihazKategorileri = []string{"SÜRÜCÜ", "SENSÖR", "PANEL", "I/O MODÜL", "ENDÜSTRİYEL PC", "DİĞER"}

// checkCihazKategorisi, verilen kategorinin geçerli olup olmadığını kontrol eder.
func checkCihazKategorisi(kategori string) bool {
	for _, validKategori := range validCihazKategorileri {
		if kategori == validKategori {
			return true
		}
	}
	return false
}

func main() {
	db, err := sql.Open("sqlite3", "./envanter.db")
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	sqlStmt := `
    CREATE TABLE IF NOT EXISTS Envanter (
        Id INTEGER PRIMARY KEY AUTOINCREMENT,
        Durum TEXT,
        SurecDurumu TEXT,
        CihazDurumu TEXT,
        CikisDurumu TEXT,
        Barkod TEXT,
        CihazKategorisi TEXT,
        CihazBilgisi TEXT,
        SeriNo TEXT,
        GirisTarihi TEXT
    );
    `
	_, err = db.Exec(sqlStmt)
	if err != nil {
		log.Printf("%q: %s\n", err, sqlStmt)
		return
	}

	fmt.Println("SQLite veritabanı ve Envanter tablosu hazır!")

	http.HandleFunc("/api/envanter/ekle", func(w http.ResponseWriter, r *http.Request) {
		addItemHandler(w, r, db)
	})

	http.HandleFunc("/api/envanter/listele", func(w http.ResponseWriter, r *http.Request) {
		listItemsHandler(w, r, db)
	})

	http.HandleFunc("/api/envanter/guncelle", func(w http.ResponseWriter, r *http.Request) {
		updateItemHandler(w, r, db)
	})

	http.HandleFunc("/api/envanter/sil", func(w http.ResponseWriter, r *http.Request) {
		deleteItemHandler(w, r, db)
	})

	fs := http.FileServer(http.Dir("../frontend"))
	http.Handle("/", fs)

	fmt.Println("Sunucu 8080 portunda dinliyor...")
	log.Fatal(http.ListenAndServe(":8080", nil))
}

func listItemsHandler(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	if r.Method != http.MethodGet {
		http.Error(w, "Sadece GET metodu desteklenir", http.StatusMethodNotAllowed)
		return
	}

	query := r.URL.Query()
	kategori := query.Get("kategori")

	var rows *sql.Rows
	var err error

	if kategori != "" {
		rows, err = db.Query("SELECT Id, Durum, SurecDurumu, CihazDurumu, CikisDurumu, Barkod, CihazKategorisi, CihazBilgisi, SeriNo, GirisTarihi FROM Envanter WHERE CihazKategorisi = ?", kategori)
	} else {
		rows, err = db.Query("SELECT Id, Durum, SurecDurumu, CihazDurumu, CikisDurumu, Barkod, CihazKategorisi, CihazBilgisi, SeriNo, GirisTarihi FROM Envanter")
	}

	if err != nil {
		http.Error(w, "Veritabanı sorgusu hatası: "+err.Error(), http.StatusInternalServerError)
		return
	}
	defer rows.Close()

	var envanterItems []EnvanterItem
	for rows.Next() {
		var item EnvanterItem
		err := rows.Scan(
			&item.Id,
			&item.Durum,
			&item.SurecDurumu,
			&item.CihazDurumu,
			&item.CikisDurumu,
			&item.Barkod,
			&item.CihazKategorisi,
			&item.CihazBilgisi,
			&item.SeriNo,
			&item.GirisTarihi,
		)
		if err != nil {
			http.Error(w, "Veri okuma hatası: "+err.Error(), http.StatusInternalServerError)
			return
		}
		envanterItems = append(envanterItems, item)
	}

	w.Header().Set("Content-Type", "application/json")
	w.Header().Set("Access-Control-Allow-Origin", "*")
	json.NewEncoder(w).Encode(envanterItems)
}

func addItemHandler(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	if r.Method != http.MethodPost {
		http.Error(w, "Sadece POST metodu desteklenir", http.StatusMethodNotAllowed)
		return
	}

	var newItem EnvanterItem
	err := json.NewDecoder(r.Body).Decode(&newItem)
	if err != nil {
		http.Error(w, "Geçersiz JSON verisi: "+err.Error(), http.StatusBadRequest)
		return
	}

	if !checkCihazKategorisi(newItem.CihazKategorisi) {
		http.Error(w, "Geçersiz Cihaz Kategorisi. Kabul edilen değerler: SÜRÜCÜ, SENSÖR, PANEL, I/O MODÜL, ENDÜSTRİYEL PC, DİĞER", http.StatusBadRequest)
		return
	}

	stmt, err := db.Prepare("INSERT INTO Envanter(Durum, SurecDurumu, CihazDurumu, CikisDurumu, Barkod, CihazKategorisi, CihazBilgisi, SeriNo, GirisTarihi) values(?, ?, ?, ?, ?, ?, ?, ?, ?)")
	if err != nil {
		http.Error(w, "SQL sorgusu hazırlanamadı: "+err.Error(), http.StatusInternalServerError)
		return
	}
	defer stmt.Close()

	_, err = stmt.Exec(
		newItem.Durum,
		newItem.SurecDurumu,
		newItem.CihazDurumu,
		newItem.CikisDurumu,
		newItem.Barkod,
		newItem.CihazKategorisi,
		newItem.CihazBilgisi,
		newItem.SeriNo,
		newItem.GirisTarihi,
	)

	if err != nil {
		http.Error(w, "Veritabanına ekleme hatası: "+err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusCreated)
	w.Header().Set("Content-Type", "application/json")
	w.Header().Set("Access-Control-Allow-Origin", "*")
	json.NewEncoder(w).Encode(map[string]string{"message": "Kayıt başarıyla eklendi!"})
}

func updateItemHandler(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	if r.Method != http.MethodPut && r.Method != http.MethodPatch {
		http.Error(w, "Sadece PUT veya PATCH metodu desteklenir", http.StatusMethodNotAllowed)
		return
	}

	var updatedItem EnvanterItem
	err := json.NewDecoder(r.Body).Decode(&updatedItem)
	if err != nil {
		http.Error(w, "Geçersiz JSON verisi: "+err.Error(), http.StatusBadRequest)
		return
	}

	if !checkCihazKategorisi(updatedItem.CihazKategorisi) {
		http.Error(w, "Geçersiz Cihaz Kategorisi. Kabul edilen değerler: SÜRÜCÜ, SENSÖR, PANEL, I/O MODÜL, ENDÜSTRİYEL PC, DİĞER", http.StatusBadRequest)
		return
	}

	if updatedItem.Id == 0 {
		http.Error(w, "Güncellenecek öğenin ID'si belirtilmelidir", http.StatusBadRequest)
		return
	}

	stmt, err := db.Prepare("UPDATE Envanter SET Durum=?, SurecDurumu=?, CihazDurumu=?, CikisDurumu=?, Barkod=?, CihazKategorisi=?, CihazBilgisi=?, SeriNo=?, GirisTarihi=? WHERE Id=?")
	if err != nil {
		http.Error(w, "SQL sorgusu hazırlanamadı: "+err.Error(), http.StatusInternalServerError)
		return
	}
	defer stmt.Close()

	result, err := stmt.Exec(
		updatedItem.Durum,
		updatedItem.SurecDurumu,
		updatedItem.CihazDurumu,
		updatedItem.CikisDurumu,
		updatedItem.Barkod,
		updatedItem.CihazKategorisi,
		updatedItem.CihazBilgisi,
		updatedItem.SeriNo,
		updatedItem.GirisTarihi,
		updatedItem.Id,
	)

	if err != nil {
		http.Error(w, "Veritabanı güncelleme hatası: "+err.Error(), http.StatusInternalServerError)
		return
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		http.Error(w, "Güncellenen satır sayısı alınamadı: "+err.Error(), http.StatusInternalServerError)
		return
	}

	if rowsAffected == 0 {
		http.Error(w, "Belirtilen ID ile eşleşen kayıt bulunamadı", http.StatusNotFound)
		return
	}

	w.WriteHeader(http.StatusOK)
	w.Header().Set("Content-Type", "application/json")
	w.Header().Set("Access-Control-Allow-Origin", "*")
	json.NewEncoder(w).Encode(map[string]string{"message": "Kayıt başarıyla güncellendi!"})
}

func deleteItemHandler(w http.ResponseWriter, r *http.Request, db *sql.DB) {
	if r.Method != http.MethodDelete {
		http.Error(w, "Sadece DELETE metodu desteklenir", http.StatusMethodNotAllowed)
		return
	}

	query := r.URL.Query()
	idStr := query.Get("id")
	if idStr == "" {
		http.Error(w, "Silinecek öğenin ID'si belirtilmelidir", http.StatusBadRequest)
		return
	}

	id, err := strconv.Atoi(idStr)
	if err != nil {
		http.Error(w, "Geçersiz ID formatı", http.StatusBadRequest)
		return
	}

	stmt, err := db.Prepare("DELETE FROM Envanter WHERE Id=?")
	if err != nil {
		http.Error(w, "SQL sorgusu hazırlanamadı: "+err.Error(), http.StatusInternalServerError)
		return
	}
	defer stmt.Close()

	result, err := stmt.Exec(id)
	if err != nil {
		http.Error(w, "Veritabanı silme hatası: "+err.Error(), http.StatusInternalServerError)
		return
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		http.Error(w, "Silinen satır sayısı alınamadı: "+err.Error(), http.StatusInternalServerError)
		return
	}

	if rowsAffected == 0 {
		http.Error(w, "Belirtilen ID ile eşleşen kayıt bulunamadı", http.StatusNotFound)
		return
	}

	w.WriteHeader(http.StatusOK)
	w.Header().Set("Content-Type", "application/json")
	w.Header().Set("Access-Control-Allow-Origin", "*")
	json.NewEncoder(w).Encode(map[string]string{"message": "Kayıt başarıyla silindi!"})
}
