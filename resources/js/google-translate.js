export const googleTranslate = {
    async translate(strSourceText, target) {
        var url = `https://translation.googleapis.com/language/translate/v2?key=${process.env.MIX_KEY_GOOGLE_TRANSLATOR}`
        let response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                target: target,
                q: strSourceText
            }),
        });
        let data = await response.json()
        if (data.error) {
            console.error('Erro na tradução:', data.error.message);
            return { data:{ translations: [{ translatedText: strSourceText }]} };
        } else {
            return data;
        }
    },
}
