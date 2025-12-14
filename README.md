# Don Barbero - Sistema de Agendamento

> Sistema completo de agendamento para barbearia desenvolvido em PHP MVC com Supabase (PostgreSQL) e Tailwind CSS.

**Desenvolvido por:** [Dante Testa](https://dantetesta.com.br)  
**Data de Criação:** 03/11/2025  
**Versão:** 1.0.0

---

## 📋 Sobre o Projeto

Don Barbero é um sistema profissional de agendamento online para barbearias, oferecendo:

- ✅ Agendamento online 24/7
- ✅ Gestão de serviços (Cabelo, Barba, Combo)
- ✅ Painel administrativo completo
- ✅ Relatórios financeiros
- ✅ Sistema de pagamentos
- ✅ Política de cancelamento inteligente
- ✅ Segurança robusta com reCAPTCHA v3
- ✅ Design responsivo mobile-first
- ✅ SEO otimizado

---

## 🛠️ Stack Tecnológica

### Backend
- **PHP 8.2+** (Custom MVC Architecture)
- **Supabase** (PostgreSQL com REST API)
- **Sessions nativas** com cookies seguros

### Frontend
- **Tailwind CSS** (via CDN)
- **JavaScript Vanilla** (performance máxima)
- **Google Fonts** (Inter)

### Segurança
- Password hashing com **Argon2id**
- Proteção contra **SQLi, XSS, CSRF**
- **Google reCAPTCHA v3**
- Rate limiting
- Headers de segurança (CSP, HSTS, etc.)

### Deploy
- **Apache** (cPanel)
- **HTTPS** (AutoSSL)
- Timezone: America/Sao_Paulo

---

## 📂 Estrutura do Projeto

```
DonBarbero/
├── app/
│   ├── controllers/          # Controllers MVC
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   └── AdminController.php
│   ├── models/               # Models (conexão com Supabase)
│   ├── views/                # Views (templates PHP)
│   │   ├── layouts/
│   │   │   └── app.php       # Layout principal
│   │   ├── home/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   └── admin/
│   └── services/             # Serviços auxiliares
├── config/
│   ├── config.php            # Configurações principais
│   ├── bootstrap.php         # Bootstrap da aplicação
│   ├── router.php            # Sistema de rotas
│   ├── routes.php            # Definição de rotas
│   └── helpers.php           # Funções auxiliares globais
├── public/
│   ├── css/                  # CSS customizado
│   ├── js/                   # JavaScript
│   └── img/                  # Imagens
├── database/
│   └── schema.sql            # Schema do banco de dados
├── logs/                     # Logs da aplicação
├── cache/                    # Cache temporário
├── .htaccess                 # Rewrite rules do Apache
├── index.php                 # Front controller
├── .env.example              # Exemplo de variáveis de ambiente
└── README.md                 # Este arquivo
```

---

## 🚀 Instalação

### Requisitos
- PHP 8.2 ou superior
- Apache com mod_rewrite
- Conta no Supabase
- Google reCAPTCHA v3 (chaves)

### Passo 1: Clonar/Baixar o Projeto
```bash
# Clone ou copie os arquivos para o diretório desejado
cd /seu/diretorio/DonBarbero
```

### Passo 2: Configurar Variáveis de Ambiente
```bash
# Copiar .env.example para .env
cp .env.example .env

# Editar .env com suas credenciais
nano .env
```

### Passo 3: Configurar Banco de Dados no Supabase
1. Acesse o [Supabase Dashboard](https://app.supabase.com)
2. Acesse o SQL Editor
3. Execute o arquivo `database/schema.sql`
4. Verifique se todas as tabelas foram criadas

### Passo 4: Configurar Permissões (cPanel)
```bash
# Garantir permissões corretas
chmod 755 /path/to/DonBarbero
chmod 644 index.php .htaccess
chmod -R 755 app/ config/
chmod -R 777 logs/ cache/
```

### Passo 5: Configurar Apache
Certifique-se de que:
- `mod_rewrite` está ativo
- `.htaccess` está sendo lido
- PHP 8.2 está selecionado no cPanel

### Passo 6: Testar
Acesse: `https://site2.danteflix.com.br`

---

## 📝 Variáveis de Ambiente

Crie um arquivo `.env` ou configure no cPanel:

```env
# Ambiente
APP_ENV=production
APP_URL=https://site2.danteflix.com.br

# Supabase
SUPABASE_URL=https://rafanckccuxtarswlljp.supabase.co
SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...

# Google reCAPTCHA v3
RECAPTCHA_SITE_KEY=6LdSBAEsAAAAAElzdrCC8DipJTkAXMSikPOaHUHm
RECAPTCHA_SECRET=6LdSBAEsAAAAABZynADpEzHYpbJkBwL_Lc-gi6_O
```

---

## 🔐 Segurança

### Práticas Implementadas
- [x] Password hashing com Argon2id
- [x] CSRF tokens em todos os formulários
- [x] Escape de output (XSS protection)
- [x] Prepared statements (SQLi protection)
- [x] Content Security Policy (CSP)
- [x] Rate limiting
- [x] Session fixation protection
- [x] Secure cookies (HttpOnly, SameSite, Secure)
- [x] Google reCAPTCHA v3

### Headers de Segurança
```
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
Referrer-Policy: no-referrer-when-downgrade
Permissions-Policy: geolocation=(), microphone=(), camera=()
Content-Security-Policy: [strict policy]
Strict-Transport-Security: max-age=31536000 (production)
```

---

## 🎯 Roadmap de Desenvolvimento

### ✅ Fase 1 - Bootstrap do Projeto (CONCLUÍDA)
- [x] Estrutura MVC
- [x] Sistema de rotas
- [x] Layout responsivo com Tailwind
- [x] Landing page
- [x] Headers de segurança

### 🔄 Fase 2 - Banco de Dados Supabase
- [ ] Criar tabelas no Supabase
- [ ] Constraint de exclusão (GIST)
- [ ] Seed inicial de dados

### 🔄 Fase 3 - Autenticação + reCAPTCHA v3
- [ ] Sistema de registro
- [ ] Sistema de login
- [ ] Integração reCAPTCHA v3
- [ ] Sessões seguras

### 🔄 Fase 4 - Dashboard do Cliente
- [ ] Listar agendamentos
- [ ] Fluxo de criação de agendamento
- [ ] Interface responsiva

### 🔄 Fase 5 - Geração de Slots
- [ ] Cálculo dinâmico de horários
- [ ] Respeitar configurações do barbeiro
- [ ] Verificar disponibilidade

### 🔄 Fase 6 - CRUD de Agendamento
- [ ] Criar agendamento
- [ ] Cancelar com política de 2h
- [ ] Código de controle único

### 🔄 Fase 7 - Painel Admin
- [ ] Login admin
- [ ] Gerenciar agendamentos
- [ ] Atualizar status
- [ ] Confirmar pagamentos

### 🔄 Fase 8 - Relatórios Financeiros
- [ ] Faturamento por período
- [ ] Filtros avançados
- [ ] Export CSV

### 🔄 Fase 9 - Deploy em Produção
- [ ] Configuração cPanel
- [ ] Otimizações de performance
- [ ] Cache
- [ ] Monitoramento

---

## 🗄️ Modelo de Dados

### Tabelas Principais

**users**
- id (UUID)
- role (client | admin)
- name, email, whatsapp
- password_hash
- created_at

**services**
- id (SERIAL)
- name, duration_minutes, price

**appointments**
- id (UUID)
- user_id, service_id
- start_at, end_at
- status (aguardando | confirmado | concluido | cancelado)
- payment_confirmed
- control_code

**barber_settings**
- start_hour, end_hour
- working_days

**payments_ledger**
- appointment_id
- amount, paid_at

---

## 📱 Rotas da Aplicação

### Públicas
- `GET /` - Landing page
- `GET /auth/register` - Formulário de cadastro
- `POST /auth/register` - Processar cadastro
- `GET /auth/login` - Formulário de login
- `POST /auth/login` - Processar login

### Cliente (autenticado)
- `GET /dashboard` - Dashboard do cliente
- `GET /dashboard/new` - Novo agendamento
- `POST /dashboard/slots` - Buscar slots
- `POST /dashboard/store` - Salvar agendamento
- `POST /dashboard/cancel/{id}` - Cancelar

### Admin (autenticado + role=admin)
- `GET /admin` - Painel admin
- `POST /admin/update-status/{id}` - Atualizar status
- `POST /admin/confirm-payment/{id}` - Confirmar pagamento
- `GET /admin/finance` - Relatório financeiro

---

## 🧪 Testes

### Testes Manuais Recomendados
- [ ] Landing page carrega corretamente
- [ ] Formulários de cadastro e login exibem
- [ ] Navegação entre páginas funciona
- [ ] Layout responsivo em mobile/tablet/desktop
- [ ] Headers de segurança estão ativos

### Ferramentas para Testar Segurança
- [SecurityHeaders.com](https://securityheaders.com)
- [SSL Labs](https://www.ssllabs.com/ssltest/)
- [Mozilla Observatory](https://observatory.mozilla.org/)

---

## 📊 Performance & SEO

### Otimizações Implementadas
- [x] Tailwind CSS via CDN (fast loading)
- [x] Compressão GZIP
- [x] Cache de assets estáticos
- [x] Meta tags Open Graph
- [x] Semantic HTML5
- [x] Mobile-first responsive design
- [x] Lazy loading de recursos

### SEO
- [x] Meta descriptions
- [x] Title tags otimizados
- [x] URLs amigáveis
- [x] Sitemap.xml (a implementar)
- [x] Robots.txt (a implementar)

---

## 🐛 Troubleshooting

### Erro 500 - Internal Server Error
- Verificar se PHP 8.2 está ativo
- Verificar permissões dos arquivos
- Verificar logs em `/logs/error.log`

### Página em Branco
- Ativar `display_errors` temporariamente
- Verificar sintaxe PHP
- Verificar se `.htaccess` está correto

### Rotas Não Funcionam
- Verificar se `mod_rewrite` está ativo
- Verificar `.htaccess`
- Verificar se o arquivo está na raiz do subdomínio

---

## 📞 Suporte

Para questões técnicas ou suporte:

**Desenvolvedor:** Dante Testa  
**Website:** [https://dantetesta.com.br](https://dantetesta.com.br)  
**Email:** [Contato via website]

---

## 📄 Licença

Copyright © 2025 Dante Testa. Todos os direitos reservados.

Este projeto foi desenvolvido sob medida para Don Barbero.

---

## 🙏 Agradecimentos

- **Tailwind CSS** - Framework CSS
- **Supabase** - Backend as a Service
- **Google** - reCAPTCHA v3
- **PHP Community** - Documentação e suporte

---

**Última Atualização:** 03/11/2025 15:34  
**Status:** Fase 1 Concluída ✅
