# WC WhatsApp Notifications

Plugin WordPress que envia notificações de pedido WooCommerce direto no WhatsApp do cliente — sem depender da API oficial do Meta, sem aprovação de template e sem mensalidade de gateway.

Usa a [Evolution API](https://doc.evolution-api.com/) com o protocolo Baileys (WhatsApp Web), que você mesmo hospeda.

---

## Como funciona

Quando o status de um pedido muda no WooCommerce, o plugin dispara uma mensagem WhatsApp pro número de telefone cadastrado no pedido. Simples assim.

```
Pedido #42 → status "Processando"
→ cliente recebe: "Olá, Ana! ✅ Pagamento confirmado. Seu pedido #42 já está sendo preparado."
```

Cada status tem um template próprio, editável direto no painel do WordPress. As variáveis `{nome}`, `{pedido}`, `{total}`, `{status}` e `{site}` são substituídas automaticamente.

Além das notificações, o plugin inclui um **widget de chat flutuante** — aquele botão de WhatsApp no canto da tela que abre uma conversa com mensagem pré-preenchida.

---

## Requisitos

- WordPress 6.0+
- PHP 8.0+
- WooCommerce 7.0+
- Uma instância da [Evolution API](https://doc.evolution-api.com/) rodando (local ou em servidor)

---

## Instalação

**Via git** (recomendado pra desenvolvimento):

```bash
git clone https://github.com/larissa4p/wc-whatsapp-notifications wp-content/plugins/evolution-whatsapp
```

**Manual**: baixe o zip, extraia em `wp-content/plugins/evolution-whatsapp` e ative em **Plugins → Plugins instalados**.

---

## Configuração

Após ativar, vá em **Configurações → Evolution WhatsApp**.

### 1. Conexão com a Evolution API

| Campo | O que colocar |
|---|---|
| URL da API | Endereço da sua instância. Ex: `http://localhost:8080` |
| API Key | A chave definida no `docker-compose.yml` ou no painel da Evolution |
| Nome da instância | O nome exato que você deu à instância ao criar |

O plugin mostra o status da conexão em tempo real na própria página de configurações.

### 2. Templates de mensagem

Cada status do WooCommerce tem um template separado com opção de ativar/desativar individualmente. Os templates padrão já estão preenchidos, mas você pode editar livremente.

**Variáveis disponíveis:**

| Variável | Valor |
|---|---|
| `{nome}` | Primeiro nome do cliente |
| `{pedido}` | Número do pedido |
| `{total}` | Valor total formatado (ex: R$ 129,90) |
| `{status}` | Nome do status atual |
| `{site}` | Nome do site |

### 3. Widget de chat

Configure no mesmo painel: número de destino, mensagem pré-preenchida, cor do botão, label e posição (inferior direito ou esquerdo).

---

## Rodando a Evolution API localmente com Docker

Se você não tem uma instância da Evolution API ainda, o jeito mais rápido é subir com Docker:

```yaml
# docker-compose.yml
services:
  evolution-postgres:
    image: postgres:15
    environment:
      POSTGRES_DB: evolution
      POSTGRES_USER: evolution
      POSTGRES_PASSWORD: evolution123
    volumes:
      - evolution_pg_data:/var/lib/postgresql/data

  evolution-api:
    image: evoapicloud/evolution-api:latest
    ports:
      - "8080:8080"
    environment:
      DATABASE_CONNECTION_URI: postgresql://evolution:evolution123@evolution-postgres:5432/evolution
      DATABASE_CONNECTION_CLIENT_NAME: evolution_exchange
      AUTHENTICATION_API_KEY: sua-chave-aqui
      LOG_LEVEL: error
    depends_on:
      - evolution-postgres

volumes:
  evolution_pg_data:
```

```bash
docker compose up -d
```

A API fica disponível em `http://localhost:8080`. Acesse o painel em `http://localhost:8080/manager` pra criar e conectar a instância via QR Code.

---

## Por que Evolution API e não a API oficial do Meta?

A API oficial do WhatsApp Business exige aprovação de conta, aprovação de templates de mensagem e cobra por conversa iniciada. Pra pequenas lojas que só querem avisar o cliente que o pedido foi confirmado, isso é burocracia demais.

A Evolution API usa o protocolo Baileys (WhatsApp Web) — você conecta um número comum via QR Code e manda mensagens normalmente, como se fosse pelo celular. Sem aprovação, sem template pré-cadastrado, sem custo por mensagem.

A contrapartida: você precisa hospedar a instância. Um VPS de R$ 25/mês ou serviços como Railway e Render resolvem.

---

## Licença

GPL v2 or later.
