<div align="center">

# DURK

**Streetwear Underground**

*Do asfalto pra rua.*

—

</div>

Uma loja online de streetwear feita pra quem vive a cultura urbana — não como tendência, mas como identidade. Roupas com alma de quebrada, atitude na peça e zero compromisso com padrão.

Este repositório é o e-commerce completo da marca: vitrine pública, carrinho, finalização de pedido e um painel administrativo pra gerenciar tudo por dentro.

<br>

## A ideia

> *"Não é só roupa. É identidade."*

A DURK nasceu da vontade de traduzir em código aquilo que a rua já sabe: estilo é fala, é grito, é resistência. Por isso o projeto não é só um catálogo bonito — é uma experiência inteira, do primeiro scroll ao "pedido confirmado".

<br>

## O que tem aqui dentro

**Pro cliente**
- Página inicial com drops em destaque
- Catálogo com filtros por categoria, gênero e tamanho
- Busca rápida
- Detalhes de produto com galeria e seleção de variação
- Carrinho com persistência
- Favoritos
- Cadastro, login e área "minha conta"
- Endereços salvos
- Finalização de pedido e histórico

**Pro admin**
- Dashboard com visão geral
- CRUD de produtos, categorias e filtros
- Gestão de pedidos
- Gestão de usuários

<br>

## Stack

```
Backend     PHP 8 + MySQL (mysqli)
Frontend    HTML semântico + Tailwind CSS
Ícones      Font Awesome
Ambiente    XAMPP (Apache + MySQL)
```

Sem framework. Sem build. Só o essencial pra rodar — e isso é proposital.

<br>

## Como rodar

**1.** Clone na pasta `htdocs` do seu XAMPP:

```bash
git clone <repo> C:/xampp/htdocs/Loja-Durk
```

**2.** Inicie o **Apache** e o **MySQL** no painel do XAMPP.

**3.** Importe o banco em `http://localhost/phpmyadmin`:

```
arquivo: durk.sql
```

**4.** Abra no navegador:

```
Loja      →  http://localhost/Loja-Durk/views/index.php
Admin     →  http://localhost/Loja-Durk/admin/index.php
```

<br>

## Estrutura

```
Loja-Durk/
│
├─ views/        ← páginas da loja (cliente)
├─ admin/        ← painel administrativo
├─ backend/      ← conexão, AJAX e processamento
├─ includes/     ← header, footer e auth
├─ imagens/      ← assets visuais
├─ config.php    ← BASE_URL do projeto
└─ durk.sql      ← schema + seeds do banco
```

<br>

## Notas

- A conexão padrão usa `root` sem senha — ajuste em `backend/db.php` antes de subir pra produção.
- O `BASE_URL` em `config.php` precisa bater com o nome da pasta dentro do `htdocs`.
- Senhas são armazenadas com hash. Nada de texto plano por aqui.

<br>

---

<div align="center">

*Feito com café, drill no fone e respeito pela rua.*

**DURK** · Drop 01 · SS26

</div>
