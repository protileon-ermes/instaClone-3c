# 🚀 Exemplos de Requisições Thunder Client - InstaClone API

## Base URL
```
http://localhost:8003/api
```

---

## 📝 1. AUTENTICAÇÃO

### 1.1 Registrar Novo Usuário
**Método:** POST  
**URL:** `http://localhost:8003/api/auth/register`  
**Headers:**
```
Content-Type: application/json
```
**Body (JSON):**
```json
{
  "name": "João Silva",
  "username": "joao_silva",
  "email": "joao@example.com",
  "password": "senha123456",
  "password_confirmation": "senha123456"
}
```
**Response (201):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "João Silva",
    "email": "joao@example.com",
    "username": "joao_silva"
  }
}
```

---

### 1.2 Fazer Login
**Método:** POST  
**URL:** `http://localhost:8003/api/auth/login`  
**Headers:**
```
Content-Type: application/json
```
**Body (JSON):**
```json
{
  "email": "joao@example.com",
  "password": "senha123456"
}
```
**Response (200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "João Silva",
    "email": "joao@example.com",
    "username": "joao_silva"
  }
}
```

---

### 1.3 Obter Dados do Usuário Autenticado
**Método:** GET  
**URL:** `http://localhost:8003/api/auth/me`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
```
**Response (200):**
```json
{
  "id": 1,
  "name": "João Silva",
  "email": "joao@example.com",
  "username": "joao_silva",
  "avatar": "/storage/avatars/joao.jpg",
  "bio": "Fotografo e viajante"
}
```

---

### 1.4 Fazer Logout
**Método:** POST  
**URL:** `http://localhost:8003/api/auth/logout`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
```
**Response (200):**
```json
{
  "message": "Token deletado com sucesso"
}
```

---

### 1.5 Renovar Token
**Método:** POST  
**URL:** `http://localhost:8003/api/auth/refresh`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
```
**Response (200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "Bearer"
}
```

---

## 👤 2. USUÁRIOS

### 2.1 Buscar Usuários
**Método:** GET  
**URL:** `http://localhost:8003/api/users/search?q=joao`  
**Parâmetros Query:**
- `q`: termo de busca (obrigatório)
- `page`: número da página (opcional, padrão: 1)

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "João Silva",
      "username": "joao_silva",
      "email": "joao@example.com",
      "avatar": "/storage/avatars/joao.jpg",
      "bio": "Fotografo e viajante",
      "created_at": "2026-05-22T10:30:00Z"
    }
  ],
  "last_page": 1,
  "per_page": 10,
  "total": 1
}
```

---

### 2.2 Obter Perfil de Usuário
**Método:** GET  
**URL:** `http://localhost:8003/api/users/joao_silva`  
**Response (200):**
```json
{
  "id": 1,
  "name": "João Silva",
  "username": "joao_silva",
  "email": "joao@example.com",
  "avatar": "/storage/avatars/joao.jpg",
  "bio": "Fotografo e viajante",
  "followers_count": 125,
  "following_count": 89,
  "posts_count": 42,
  "created_at": "2026-05-22T10:30:00Z"
}
```

---

### 2.3 Atualizar Meu Perfil
**Método:** PUT  
**URL:** `http://localhost:8003/api/users/me`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
Content-Type: application/json
```
**Body (JSON):**
```json
{
  "name": "João Silva Santos",
  "username": "joao_silva_2",
  "bio": "Fotografo profissional e amante de viagens 📸"
}
```
**Response (200):**
```json
{
  "id": 1,
  "name": "João Silva Santos",
  "username": "joao_silva_2",
  "email": "joao@example.com",
  "avatar": "/storage/avatars/joao.jpg",
  "bio": "Fotografo profissional e amante de viagens 📸",
  "updated_at": "2026-05-22T11:45:00Z"
}
```

---

### 2.4 Atualizar Avatar
**Método:** POST  
**URL:** `http://localhost:8003/api/users/me/avatar`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
Content-Type: multipart/form-data
```
**Form Data:**
- `avatar`: (selecione um arquivo de imagem - JPEG, PNG, JPG, WebP, máx 2MB)

**Response (200):**
```json
{
  "id": 1,
  "name": "João Silva",
  "username": "joao_silva",
  "avatar": "/storage/avatars/joao_1234567890.jpg",
  "bio": "Fotografo e viajante",
  "updated_at": "2026-05-22T11:50:00Z"
}
```

---

## 🔗 3. SISTEMA DE FOLLOW

### 3.1 Seguir Usuário
**Método:** POST  
**URL:** `http://localhost:8003/api/users/2/follow`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
```
**Response (200):**
```json
{
  "message": "Seguindo com sucesso"
}
```

---

### 3.2 Deixar de Seguir
**Método:** DELETE  
**URL:** `http://localhost:8003/api/users/2/unfollow`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
```
**Response (200):**
```json
{
  "message": "Deixou de seguir"
}
```

---

### 3.3 Verificar se está Seguindo
**Método:** GET  
**URL:** `http://localhost:8003/api/users/2/is-following`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
```
**Response (200):**
```json
{
  "is_following": true
}
```

---

### 3.4 Listar Seguidores
**Método:** GET  
**URL:** `http://localhost:8003/api/users/1/followers?page=1`  
**Parâmetros Query:**
- `page`: número da página (opcional, padrão: 1)

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 2,
      "name": "Maria Silva",
      "username": "maria_silva",
      "email": "maria@example.com",
      "avatar": "/storage/avatars/maria.jpg",
      "bio": "Professora",
      "created_at": "2026-05-20T15:45:00Z"
    }
  ],
  "last_page": 1,
  "per_page": 20,
  "total": 1
}
```

---

### 3.5 Listar Usuários que Estou Seguindo
**Método:** GET  
**URL:** `http://localhost:8003/api/users/1/following?page=1`  
**Response (200):** (mesmo formato que seguidores)

---

## 📸 4. POSTS

### 4.1 Criar Post
**Método:** POST  
**URL:** `http://localhost:8003/api/posts`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
Content-Type: multipart/form-data
```
**Form Data:**
- `image`: (arquivo de imagem obrigatório - máx 5MB)
- `caption`: Que dia lindo! ☀️ #natureza #viagem (opcional, máx 2200 caracteres)

**Response (201):**
```json
{
  "id": 1,
  "user_id": 1,
  "caption": "Que dia lindo! ☀️ #natureza #viagem",
  "image_url": "/storage/posts/image_1.jpg",
  "likes_count": 0,
  "comments_count": 0,
  "created_at": "2026-05-22T10:30:00Z"
}
```

---

### 4.2 Obter Detalhes do Post
**Método:** GET  
**URL:** `http://localhost:8003/api/posts/1`  
**Response (200):**
```json
{
  "id": 1,
  "user_id": 1,
  "caption": "Que dia lindo! ☀️ #natureza #viagem",
  "image_url": "/storage/posts/image_1.jpg",
  "likes_count": 42,
  "comments_count": 8,
  "created_at": "2026-05-22T10:30:00Z",
  "user": {
    "id": 1,
    "name": "João Silva",
    "username": "joao_silva",
    "avatar": "/storage/avatars/joao.jpg",
    "bio": "Fotografo e viajante"
  }
}
```

---

### 4.3 Listar Posts do Usuário
**Método:** GET  
**URL:** `http://localhost:8003/api/users/1/posts?page=1`  
**Parâmetros Query:**
- `page`: número da página (opcional, padrão: 1)

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "caption": "Que dia lindo! ☀️",
      "image_url": "/storage/posts/image_1.jpg",
      "likes_count": 42,
      "comments_count": 8,
      "created_at": "2026-05-22T10:30:00Z"
    }
  ],
  "last_page": 5,
  "total": 56
}
```

---

### 4.4 Atualizar Post
**Método:** PUT  
**URL:** `http://localhost:8003/api/posts/1`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
Content-Type: application/json
```
**Body (JSON):**
```json
{
  "caption": "Nova caption editada! 📸 #atualizando"
}
```
**Response (200):**
```json
{
  "id": 1,
  "user_id": 1,
  "caption": "Nova caption editada! 📸 #atualizando",
  "image_url": "/storage/posts/image_1.jpg",
  "likes_count": 42,
  "comments_count": 8,
  "updated_at": "2026-05-22T11:45:00Z"
}
```

---

### 4.5 Deletar Post
**Método:** DELETE  
**URL:** `http://localhost:8003/api/posts/1`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
```
**Response (200):**
```json
{
  "message": "Post deletado com sucesso"
}
```

---

## ❤️ 5. LIKES

### 5.1 Curtir Post
**Método:** POST  
**URL:** `http://localhost:8003/api/posts/1/like`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
```
**Response (200):**
```json
{
  "message": "Post curtido"
}
```

---

### 5.2 Descurtir Post
**Método:** DELETE  
**URL:** `http://localhost:8003/api/posts/1/unlike`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
```
**Response (200):**
```json
{
  "message": "Post descurtido"
}
```

---

### 5.3 Listar Usuários que Curtiram
**Método:** GET  
**URL:** `http://localhost:8003/api/posts/1/likes?page=1&per_page=20`  
**Parâmetros Query:**
- `page`: número da página (opcional, padrão: 1)
- `per_page`: quantidade por página (opcional, padrão: 20, máx: 100)

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 2,
      "name": "Maria Silva",
      "username": "maria_silva",
      "email": "maria@example.com",
      "avatar": "/storage/avatars/maria.jpg",
      "created_at": "2026-05-22T10:30:00Z"
    }
  ],
  "last_page": 3,
  "total": 42
}
```

---

## 💬 6. COMENTÁRIOS

### 6.1 Listar Comentários de um Post
**Método:** GET  
**URL:** `http://localhost:8003/api/posts/1/comments`  
**Response (200):**
```json
[
  {
    "id": 1,
    "user_id": 2,
    "post_id": 1,
    "body": "Ótima foto!",
    "created_at": "2026-05-22T10:35:00Z",
    "user": {
      "id": 2,
      "name": "Maria Silva",
      "username": "maria_silva",
      "avatar": "/storage/avatars/maria.jpg"
    }
  }
]
```

---

### 6.2 Criar Comentário
**Método:** POST  
**URL:** `http://localhost:8003/api/posts/1/comments`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
Content-Type: application/json
```
**Body (JSON):**
```json
{
  "body": "Adorei essa foto! Muito legal."
}
```
**Response (201):**
```json
{
  "id": 2,
  "user_id": 1,
  "post_id": 1,
  "body": "Adorei essa foto! Muito legal.",
  "created_at": "2026-05-22T10:40:00Z"
}
```

---

### 6.3 Atualizar Comentário
**Método:** PUT  
**URL:** `http://localhost:8003/api/comments/1`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
Content-Type: application/json
```
**Body (JSON):**
```json
{
  "body": "Adorei essa foto! Muito legal mesmo! 🎉"
}
```
**Response (200):**
```json
{
  "id": 1,
  "user_id": 2,
  "post_id": 1,
  "body": "Adorei essa foto! Muito legal mesmo! 🎉",
  "updated_at": "2026-05-22T10:45:00Z"
}
```

---

### 6.4 Deletar Comentário
**Método:** DELETE  
**URL:** `http://localhost:8003/api/comments/1`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
```
**Response (200):**
```json
{
  "message": "Removido com sucesso"
}
```

---

## 📰 7. FEED

### 7.1 Obter Feed Paginado
**Método:** GET  
**URL:** `http://localhost:8003/api/feed?page=1&per_page=15`  
**Headers:**
```
Authorization: Bearer {seu_token_aqui}
```
**Parâmetros Query:**
- `page`: número da página (opcional, padrão: 1)
- `per_page`: quantidade por página (opcional, padrão: 15, máx: 100)

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "user_id": 2,
      "caption": "Dia lindo no parque! 🌞",
      "image_url": "/storage/posts/image_1.jpg",
      "likes_count": 42,
      "comments_count": 8,
      "is_liked": false,
      "created_at": "2026-05-22T10:30:00Z",
      "user": {
        "id": 2,
        "name": "Maria Silva",
        "username": "maria_silva",
        "avatar": "/storage/avatars/maria.jpg",
        "is_following": true
      }
    }
  ],
  "last_page": 5,
  "total": 75
}
```

---

## 🔐 DICAS DE AUTENTICAÇÃO

1. **Salve o token após login:**
   - Copie o `access_token` do response de login
   - Use em todas as requisições protegidas com header: `Authorization: Bearer {token}`

2. **Token expira em 24 horas:**
   - Use `POST /auth/refresh` para obter um novo token

3. **Para testar no Thunder Client:**
   - Crie variáveis de ambiente com `token` = seu_token_aqui
   - Use `{{token}}` nas requisições protegidas

---

## ⚙️ CONFIGURAÇÃO DO THUNDER CLIENT

### Variáveis de Ambiente
Na aba "Env", crie:
```
baseUrl = http://localhost:8003/api
token = seu_token_aqui
userId = 1
postId = 1
commentId = 1
```

### Usando Variáveis nas URLs
```
GET {{baseUrl}}/users/{{userId}}/posts
Authorization: Bearer {{token}}
```

---

## 🧪 FLUXO DE TESTE SUGERIDO

1. **Registre um usuário:** `POST /auth/register`
2. **Copie o token** do response
3. **Atualize seu avatar:** `POST /users/me/avatar`
4. **Crie um post:** `POST /posts` (com imagem)
5. **Curta o post:** `POST /posts/1/like`
6. **Comente:** `POST /posts/1/comments`
7. **Veja o feed:** `GET /feed`
8. **Busque outros usuários:** `GET /users/search?q=...`
9. **Siga usuários:** `POST /users/2/follow`
10. **Veja o feed atualizado:** `GET /feed`

---

## 📚 REFERÊNCIA RÁPIDA

| Operação | Método | Endpoint |
|----------|--------|----------|
| Registrar | POST | `/auth/register` |
| Login | POST | `/auth/login` |
| Logout | POST | `/auth/logout` |
| Perfil | GET | `/users/{username}` |
| Posts | GET | `/users/{id}/posts` |
| Criar Post | POST | `/posts` |
| Feed | GET | `/feed` |
| Seguir | POST | `/users/{id}/follow` |
| Deixar Seguir | DELETE | `/users/{id}/unfollow` |
| Curtir | POST | `/posts/{id}/like` |
| Comentar | POST | `/posts/{id}/comments` |
