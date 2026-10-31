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
        getDashboard: () => http.get('/admin/dashboard'),
        getNaskahs: (params) => http.get('/admin/naskahs', { params }),
        getNaskahDetail: (id) => http.get(`/admin/naskahs/${id}`),
        decideNaskah: (id, data) => http.post(`/admin/naskahs/${id}/decide`, data),
        publishNaskah: (id, data) => http.post(`/admin/naskahs/${id}/publish`, data),
        getUsers: (params) => http.get('/admin/users', { params }),
        verifyUser: (id, data) => http.post(`/admin/users/${id}/verify`, data),
        deleteUser: (id) => http.delete(`/admin/users/${id}`),
        getKontributorDetail: (id) => http.get(`/admin/kontributor/${id}`),
        getAdmins: () => http.get('/admin/admins'),
        createAdmin: (data) => http.post('/admin/admins', data),
        updateAdmin: (id, data) => http.put(`/admin/admins/${id}`, data),
        deleteAdmin: (id) => http.delete(`/admin/admins/${id}`),
    },
}

export default api
