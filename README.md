# Evolution WhatsApp

Plugin WordPress para envio de notificações WooCommerce e widget de chat via [Evolution API](https://doc.evolution-api.com/).

## Funcionalidades

- ✅ **Notificações WooCommerce** — envia mensagem WhatsApp ao cliente em cada mudança de status do pedido
- 💬 **Widget de chat flutuante** — botão de WhatsApp no front-end com tooltip, animação de pulso e link `wa.me`
- ⚙️ **Templates editáveis** — mensagem personalizada por status com variáveis dinâmicas
- 🔌 **Verificação de conexão** — status da instância exibido direto na página de configurações

## Requisitos

- WordPress 6.0+
- PHP 8.0+
- WooCommerce 7.0+ (para notificações)
- Instância Evolution API ativa

## Instalação

1. Faça o upload da pasta `evolution-whatsapp` em `wp-content/plugins/`
2. Ative o plugin em **Plugins → Plugins instalados**
3. Configure em **Configurações → Evolution WhatsApp**

## Configuração

### API
| Campo | Descrição |
|-------|-----------|
| URL da Evolution API | Ex: `https://api.seuservidor.com` |
| API Key | Chave de autenticação da sua instância |
| Nome da Instância | Nome exato da instância no Evolution API |

### Templates — variáveis disponíveis

| Variável | Valor |
|----------|-------|
| `{nome}` | Primeiro nome do cliente |
| `{pedido}` | Número do pedido |
| `{total}` | Valor total formatado |
| `{status}` | Nome do status atual |
| `{site}` | Nome do site |

### Widget de Chat

Configure número, mensagem pré-preenchida, cor, label e posição (inferior direito ou esquerdo) diretamente no painel.

## Desenvolvimento

```bash
# Clonar no diretório de plugins do WordPress
git clone https://github.com/larissa4p/wp-evolution-whatsapp wp-content/plugins/evolution-whatsapp
```

## Licença

GPL v2 or later.
