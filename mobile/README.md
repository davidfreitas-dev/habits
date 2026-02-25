# 🌿 Habitus — Habit Tracking App

Aplicativo para rastrear e gerenciar seus hábitos diários, construído com Ionic, Vue 3 e Capacitor.

---

## 🌟 Tecnologias

- **[Ionic](https://ionicframework.com/)** — Framework para apps híbridos
- **[Vue 3](https://vuejs.org/)** — Framework JavaScript progressivo
- **[Capacitor](https://capacitorjs.com/)** — Runtime nativo para iOS e Android

---

## 🏗️ Arquitetura

O projeto segue uma arquitetura modular baseada em camadas, com foco em separação de responsabilidades e escalabilidade:

```text
src/
├── api/            # Configuração do cliente HTTP (Axios) e interceptores
├── components/     # Componentes Vue reutilizáveis
│   ├── ui/         # Componentes base e atômicos (Button, Input)
│   ├── habits/     # Componentes de domínio (HabitDay, Summary)
│   └── layout/     # Componentes de estrutura (Header, Container)
├── composables/    # Lógica reutilizável com Composition API
├── constants/      # Strings globais, endpoints e chaves de storage
├── lib/            # Bibliotecas externas
├── router/         # Definições de rotas e guardas de navegação
├── services/       # Camada de comunicação com a API
├── stores/         # Gerenciamento de estado global (Pinia)
├── theme/          # Estilos globais e variáveis de tema Ionic
└── views/          # Páginas organizadas por contexto (auth, habits, settings)
```

**Fluxo de dados:** Views → Stores → Services → API

---

## 🚀 Configuração Inicial

### Pré-requisitos

**Node.js 18**
```sh
node -v  # deve exibir v18.x.x
```

**Ionic CLI**
```sh
npm install -g @ionic/cli
ionic --version
```

### Instalação

```sh
git clone https://github.com/davidfreitas-dev/habits.git
cd habits
npm install
```

### Variáveis de ambiente

Crie um arquivo `.env.development` na raiz do projeto e configure `VITE_BASE_URL` conforme seu ambiente de teste:

```env
# Navegador (Web)
VITE_BASE_URL=http://localhost:8000/api/v1

# Emulador Android (10.0.2.2 mapeia para o localhost da máquina host)
VITE_BASE_URL=http://10.0.2.2:8000/api/v1

# Dispositivo físico (use o IP da sua máquina na rede local)
VITE_BASE_URL=http://192.168.x.x:8000/api/v1
```

---

## 🛠️ Comandos

### Desenvolvimento

```sh
ionic serve
```

### Build

```sh
npm run build
```

### Adicionar plataformas

```sh
npx cap add ios
npx cap add android
```

### Sincronizar

```sh
npx cap sync
```

### Executar no dispositivo/emulador

```sh
npx cap run ios
npx cap run android
```

### Live Reload

```sh
# iOS
ionic capacitor run ios -l --external

# Android
ionic capacitor run android -l --external
```

---

## 🖼️ Gerando Assets (Ícones e Splash Screen)

Os assets do app (ícones e splash screens) são gerados automaticamente pela ferramenta oficial `@capacitor/assets`.

> ⚠️ As plataformas iOS e Android precisam estar adicionadas antes de gerar os assets.

### 1. Prepare as imagens fonte

Certifique-se de ter os arquivos na pasta `assets/` na raiz do projeto com as imagens abaixo:

```text
assets/
├── icon-only.png         # Ícone do app — mínimo 1024x1024px, sem transparência
├── icon-foreground.png   # Camada de frente para Adaptive Icons (Android)
├── icon-background.png   # Camada de fundo para Adaptive Icons (Android)
├── splash.png            # Splash screen — mínimo 2732x2732px
└── splash-dark.png       # Splash screen dark mode (opcional)
```

### 2. Gere os assets

```sh
npx capacitor-assets generate
```

Isso cria automaticamente todos os tamanhos necessários dentro das pastas `ios/` e `android/`.

---

## 🔔 Personalizando o Ícone de Notificação (Android)

> ⚠️ O ícone que aparece na status bar do Android segue regras específicas do sistema: deve ser **monocromático**, com fundo transparente e design em branco. Tamanho recomendado: **24x24dp** (mas pode usar 96x96px como fonte).

**1. Defina o arquivo de imagem a ser usado como ícone da notificação**

Copie o arquivo de imagem *ic_stat_habitus.png* que se encontra na pasta `assets/` e cole na pasta `/drawable` como indicado abaixo:

```
android/app/src/main/res/drawable/ic_stat_habitus.png
```

**2. Sincronize e rebuild**

```sh
npx cap sync android
npx cap open android
```

> **iOS:** Não é possível personalizar o ícone de notificação no iOS — o sistema usa automaticamente o ícone do app.

---

## 📦 Gerando o `.ipa` para Testes (iOS)

### Pré-requisitos

- macOS com Xcode instalado
- Projeto configurado e compilável no Xcode

### Passos

1. Abra o Xcode e carregue o projeto
2. Pressione `⌘ + B` para compilar
3. Na barra lateral, acesse a pasta `Products` e localize o arquivo `.app`
4. Clique com o botão direito no `.app` → **Show in Finder**
5. Crie uma pasta chamada `Payload` e mova o `.app` para dentro dela
6. Clique com o botão direito em `Payload` → **Compress**
7. Renomeie o `.zip` gerado para `.ipa`

> ⚠️ Este método é para testes internos apenas. Para distribuição na App Store, utilize **Archive** com o certificado de distribuição adequado.

---

## 📚 Recursos

- [Documentação do Ionic](https://ionicframework.com/docs)
- [Documentação do Vue 3](https://vuejs.org/guide/)
- [Documentação do Capacitor](https://capacitorjs.com/docs)
- [Capacitor Local Notifications](https://capacitorjs.com/docs/apis/local-notifications)
- [Android Asset Studio](https://romannurik.github.io/AndroidAssetStudio/icons-notification.html)