# Quick Reference: New User Management Features

## 🎯 Main User Table Features

### Visible Columns
```
Nome | E-mail | Função | Saldo | Bloqueado | Status | Data Cadastro
```

### Filter Options
- 🎚️ Status: Active / Suspended / Inactive
- 🔒 Blocked: Yes / No / All
- 👥 Role: Admin / Affiliate / User (multi-select)
- 📅 Date Range: From - To

---

## ⚡ Individual User Actions (Dropdown Menu)

| Action | Icon | Color | Function |
|--------|------|-------|----------|
| Visualizar | 👁️ | Default | View user details |
| Editar | ✏️ | Default | Edit user info |
| Alterar Senha | 🔑 | Yellow | Reset password |
| Suspender | ⏸️ | Yellow | Suspend account |
| Ativar | ✅ | Green | Activate account |
| Bloquear/Desbloquear | 🔒/🔓 | Red/Green | Toggle block status |
| Atribuir Função | 👥 | Blue | Manage roles |
| Excluir | 🗑️ | Red | Delete user |

---

## 📦 Bulk Actions (Multiple Users)

Select users with checkboxes, then:

- ⏸️ **Suspend Selected** - Batch suspend users
- ✅ **Activate Selected** - Batch activate users  
- 🔒 **Block Selected** - Batch block users
- 🔓 **Unblock Selected** - Batch unblock users
- 🗑️ **Delete Selected** - Batch delete users

---

## 📊 Statistics Dashboard

Displays at the top of the user list:

```
┌─────────────────┬─────────────────┬─────────────────┐
│ Total Usuários  │ Usuários Ativos │ Suspensos       │
│ 1,234           │ 1,150 (93.2%)   │ 45              │
├─────────────────┼─────────────────┼─────────────────┤
│ Bloqueados      │ Novos Hoje      │ E-mails Verific.│
│ 39              │ 12              │ 980 (79.4%)     │
└─────────────────┴─────────────────┴─────────────────┘
```

---

## 🗂️ Reorganized Navigation

### Admin View
```
📊 Dashboard

👥 Gestão de Usuários
   ├─ Usuários
   ├─ Funções
   ├─ Permissões
   └─ Carteiras

💰 Financeiro
   ├─ Depósitos
   ├─ Saques
   ├─ Saques de Afiliados
   ├─ Gateway de Pagamento
   └─ SuitPay

🎮 Jogos
   ├─ Categorias
   ├─ Provedores
   └─ Jogos

✨ Recursos
   ├─ Missões
   ├─ VIP
   ├─ Banners
   └─ Personalização

⚙️ Configurações
   ├─ Gerais
   ├─ Chaves de Jogos
   ├─ Roleta
   ├─ E-mail
   └─ Avançadas

🔧 Utilitários
   └─ Limpar Cache
```

### Affiliate View
```
📊 Dashboard

💼 Afiliado
   ├─ Meus Sub-Afiliados
   ├─ Meus Saques
   └─ Link de Convite
```

---

## 📝 Enhanced User Form

### Section 1: Informações Pessoais
```
Nome*            | E-mail*          | CPF
_______________ | _______________ | ___.___.___-__

Phone            | Funções*         | Senha
(__)_____-____ | [Multi-select]  | ********
```

### Section 2: Configurações de Afiliado (Collapsed by default)
```
Afiliado         | Rev. Share (%)  | Rev. Share Fake (%)
_______________ | _____ %        | _____ %

CPA              | Baseline
R$ ______       | R$ ______
```

### Section 3: Status e Permissões
```
Status da Conta       | Bloqueado
[Dropdown: Active]    | [ ] Toggle

Influencer            | E-mail Verificado Em
[ ] Toggle            | [Date Time Picker]
```

---

## 🔐 User Status Types

| Status | Badge Color | Meaning |
|--------|-------------|---------|
| Active | 🟢 Green | Normal active user |
| Suspended | 🟡 Yellow | Temporarily suspended |
| Inactive | 🔴 Red | Inactive account |

### Status vs Blocked

- **Status**: Account activity level (active/suspended/inactive)
- **Blocked**: Complete access denial (banned = true/false)
- User can be "Active" but "Blocked" = Account exists but completely locked

---

## 💡 Quick Tips

1. **Search**: Use the global search (Ctrl+K) to find users by name or email
2. **Export**: Use bulk select → export for user data exports
3. **Shortcuts**: Click on user name to go to detail view
4. **Filters**: Combine multiple filters for precise results
5. **Notifications**: Success messages appear after each action

---

## 🚀 Common Workflows

### Suspend a Spammer
1. Find user → Actions → Suspender → Confirm

### Block Multiple Inactive Users
1. Filter by Status: Inactive
2. Select users with checkboxes
3. Bulk Actions → Block Selected → Confirm

### Create New Admin
1. Create User button → Fill form
2. In Funções field, select "admin"
3. Set Status to "Active"
4. Save

### Reset User Password
1. Find user → Actions → Alterar Senha
2. Enter new password → Save

---

## 🎨 Color Coding

- 🟢 **Green** = Success, Active, Positive
- 🔴 **Red** = Danger, Delete, Blocked
- 🟡 **Yellow** = Warning, Suspended, Caution
- 🔵 **Blue** = Information, Roles, Neutral
- ⚫ **Gray** = View, Default, Inactive
