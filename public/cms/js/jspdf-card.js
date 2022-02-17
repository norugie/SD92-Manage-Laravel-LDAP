(function() {
    var margins = {
        top: -51,
        left: -58,
        right: 0,
        bottom: 0,
        width: 800,
        height: 500
    };

    $('#download_employee_id_card').click(generateCardPDF);

    function generateCardPDF() {
        var doc = new jsPDF('l', 'mm', [85, 54]);
        var cardtoprint = document.getElementById("profile_card");

        html2canvas(cardtoprint, {
            allowTaint: true,
            useCORS: true,
            width: margins.width,
            height: margins.height,
            quality: 4
        }).then((canvas) => {
            //Canvas (convert to PNG)
            doc.addImage(canvas.toDataURL("image/png"), 'PNG', margins.left, margins.top);
            doc.save("Document.pdf");
        });

    }
})();