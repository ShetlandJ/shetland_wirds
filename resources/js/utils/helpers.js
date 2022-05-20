export const blobToBase64 = (blob) => {
    const reader = new FileReader();
    reader.readAsDataURL(blob);
    return new Promise(resolve => {
        reader.onloadend = () => {
            resolve(reader.result);
        };
    });
};

export const convertDataURIToBinary = (dataURI) => {
    const BASE64_MARKER = ';base64,';
    const base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
    const base64 = dataURI.substring(base64Index);
    const raw = window.atob(base64);
    const rawLength = raw.length;
    const array = new Uint8Array(new ArrayBuffer(rawLength));

    for (let i = 0; i < rawLength; i++) {
        array[i] = raw.charCodeAt(i);
    }
    return array;
}

export const convertToBlogUri = async (blobject) => {
    const dataURI = await blobToBase64(blobject);
    const binary = convertDataURIToBinary(dataURI);
    const blob = new Blob([binary], { type: 'audio/mp3' });
    return URL.createObjectURL(blob);
}