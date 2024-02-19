function submitreq(varid) {
    const urlParams = new URLSearchParams(window.location.search);
    const qValue = urlParams.get('q');
    const encodedQValue = encodeURIComponent(qValue);
    window.location = "./submitreq.php?id=" + varid + "&q=" + encodedQValue;
}
