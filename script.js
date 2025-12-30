document.addEventListener('DOMContentLoaded', function() {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const body = document.body;

    const currentTheme = localStorage.getItem('theme');
    if (currentTheme) {
        body.setAttribute('data-bs-theme', currentTheme);
    } else {
        body.setAttribute('data-bs-theme', 'light');
    }

    themeToggleBtn.addEventListener('click', () => {
        const currentTheme = body.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        body.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    });

    function updateContent() {
        const today = new Date();
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const formattedDate = today.toLocaleDateString('tr-TR', options);
        const dateDisplayElement = document.getElementById('date-display');
        dateDisplayElement.textContent = formattedDate;
        fetchCurrencyData();
    }

    async function fetchCurrencyData() {
        const url = 'https://api.exchangerate-api.com/v4/latest/USD';
        try {
            const response = await fetch(url);
            const data = await response.json();

            const usdToTry = data.rates.TRY;
            const eurToTry = data.rates.TRY / data.rates.EUR; 

            const usdAlis = usdToTry;
            const usdSatis = usdToTry * 1.01; 
            const eurAlis = eurToTry;
            const eurSatis = eurToTry * 1.01;

            const tickerText = `Dolar: Alış ${usdAlis.toFixed(2)} TL, Satış ${usdSatis.toFixed(2)} TL  -  Euro: Alış ${eurAlis.toFixed(2)} TL, Satış ${eurSatis.toFixed(2)} TL  `;
            
            const tickerElement = document.getElementById('currency-ticker');
            tickerElement.textContent = tickerText;
        } catch (error) {
            console.error('Döviz verisi alınırken bir hata oluştu:', error);
            const tickerElement = document.getElementById('currency-ticker');
            tickerElement.textContent = 'Döviz verisi yüklenemedi.';
        }
    }

    updateContent();
    setInterval(updateContent, 86400000); 
});