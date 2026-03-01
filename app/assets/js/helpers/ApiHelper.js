// src/assets/js/helpers/ApiHelper.js

export default class ApiHelper {
    static async fetch(url, options = {}) {
        try {
            const res = await fetch(url, options);
            const text = await res.text(); // 👈 on lit d'abord le texte brut

            let data;
            try {
                data = JSON.parse(text); // tente de parser le JSON
            } catch {
                // fallback si le serveur renvoie du texte
                data = { success: false, message: text, data: null };
            }

            if (!res.ok || data.success === false) {
                return {
                    success: false,
                    message: data.message || `Erreur HTTP ${res.status}`,
                    data: null
                };
            }

            return {
                success: data.success ?? true,
                message: data.message ?? null,
                data: data.data ?? data
            };

        } catch (err) {
            console.error(`[ApiHelper] ${url} →`, err);
            return {
                success: false,
                message: 'Erreur réseau ou serveur.',
                data: null
            };
        }
    }

    // Optionnel : méthode POST simplifiée
    static async post(url, body = {}, headers = {}) {
        return await this.fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', ...headers },
            body: JSON.stringify(body)
        });
    }

    // Optionnel : méthode GET simplifiée
    static async get(url, headers = {}) {
        return await this.fetch(url, { headers });
    }
}
