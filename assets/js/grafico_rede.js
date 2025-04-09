const ctx = document.getElementById('graficoRede').getContext('2d');
const uploadTexto = document.getElementById('uploadTexto');
const downloadTexto = document.getElementById('downloadTexto');

const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                label: 'Upload (KB/s)',
                data: [],
                borderColor: 'rgba(0, 123, 255, 0.8)',
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                fill: true,
                tension: 0.3
            },
            {
                label: 'Download (KB/s)',
                data: [],
                borderColor: 'rgba(40, 167, 69, 0.8)',
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                fill: true,
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        animation: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 1000,
                ticks: {
                    stepSize: 100
                }
            }
        },
        plugins: {
            legend: {
                labels: {
                    boxWidth: 15
                }
            }
        }
    }
});

function simularRede() {
    const agora = new Date().toLocaleTimeString();
    const upload = +(Math.random() * 300).toFixed(2);
    const download = +(Math.random() * 800).toFixed(2);

    // Atualiza os textos
    if (uploadTexto && downloadTexto) {
        uploadTexto.innerText = `${upload} KB/s`;
        downloadTexto.innerText = `${download} KB/s`;
    }

    if (chart.data.labels.length >= 20) {
        chart.data.labels.shift();
        chart.data.datasets.forEach(ds => ds.data.shift());
    }

    chart.data.labels.push(agora);
    chart.data.datasets[0].data.push(upload);
    chart.data.datasets[1].data.push(download);

    chart.update();
}

setInterval(simularRede, 5000);
