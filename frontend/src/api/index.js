import http from './axios'

const api = {
    auth: {
        login: (data) => http.post('/auth/login', data),
        register: (formData) => http.post('/auth/register-kontributor', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }),
        me: () => http.get('/auth/me'),
        logout: () => http.post('/auth/logout'),
    },

    public: {
        getBerita: () => http.get('/public/berita'),
    },

    guest: {
        kirimNaskah: (formData) => http.post('/guest/kirim-naskah', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }),
    },

    kontributor: {
        dashboard: () => http.get('/kontributor/dashboard'),
        storeNaskah: (formData) => http.post('/kontributor/naskah', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }),
    },

    admin: {
        getNaskahs: () => http.get('/admin/naskahs'),
        decideNaskah: (id, data) => http.post(`/admin/naskahs/${id}/decide`, data),
        getUsers: () => http.get('/admin/users'),
        verifyUser: (id, data) => http.post(`/admin/users/${id}/verify`, data),
    },
}

export default api

