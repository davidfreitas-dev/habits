# 🛠️ Habits - Habit Tracking App

Seja bem-vindo ao **Habits**, um aplicativo para rastrear e gerenciar seus hábitos diários. 🚀

## 🌟 Tecnologias Utilizadas

- **[Ionic](https://ionicframework.com/):** Framework para desenvolvimento de aplicativos híbridos.
- **[Vue 3](https://vuejs.org/):** Framework JavaScript progressivo para interfaces de usuário.

---

## 🏗️ Arquitetura do Projeto

O projeto segue uma arquitetura modular baseada em camadas, focada em separação de preocupações e escalabilidade:

```text
src/
├── api/            # Configuração do cliente HTTP (Axios) e interceptores
├── assets/         # Recursos estáticos (imagens, ícones, logotipos)
├── components/     # Componentes Vue reutilizáveis
│   ├── ui/         # Componentes base e atômicos (Button, Input)
│   ├── habits/     # Componentes de domínio (HabitDay, Summary)
│   └── layout/     # Componentes de estrutura (Header, Container)
├── composables/    # Lógica reutilizável (Hooks/Composition API)
├── constants/      # Strings globais, endpoints e chaves de storage
├── router/         # Definições de rotas e guardas de navegação
├── services/       # Camada de comunicação com a API (Regras de I/O)
├── stores/         # Gerenciamento de estado global (Pinia)
├── theme/          # Estilos globais e variáveis de tema do Ionic
└── views/          # Páginas organizadas por contexto (auth, habits, settings)
```

### Fluxo de Dados
1.  **Views** acionam ações nos **Stores**.
2.  **Stores** utilizam os **Services** para buscar ou enviar dados.
3.  **Services** utilizam a instância da **API** para requisições HTTP.
4.  **Constants** centralizam endpoints e chaves para evitar duplicação.

---

## 🚀 Guia de Configuração

### 1. Instale as Dependências Principais

#### Instale o Node.js (versão 18)

1. Acesse [Node.js](https://nodejs.org/) e baixe a versão 18.x.
2. Verifique se o Node.js foi instalado corretamente:
   ```sh
   node -v
   ```
   A saída deve exibir algo como `v18.x.x`.

#### Instale o Ionic CLI

1. Instale o Ionic CLI globalmente:
   ```sh
   npm install -g @ionic/cli
   ```
2. Verifique se a instalação foi bem-sucedida:
   ```sh
   ionic --version
   ```

---

### 2. Configure o Projeto

1. Clone este repositório:
   ```sh
   git clone https://github.com/davidfreitas-dev/habits.git
   cd habits
   ```

2. Instale as dependências:
   ```sh
   npm install
   ```

---

## 🛠️ Comandos Principais

### 🔧 Desenvolvimento Local

Para iniciar o servidor de desenvolvimento com recarregamento automático (Hot-Reload):
```sh
ionic serve
```

---

### 📱 Adicionar Plataformas

Adicione plataformas para iOS e Android:
```sh
npx cap add ios
npx cap add android
```

---

### 🔄 Sincronizar Arquivos

Para garantir que os arquivos do projeto estejam atualizados nas plataformas:
```sh
npx cap sync
```

---

### ▶️ Executar no Dispositivo ou Emulador

#### Executar no iOS:
```sh
npx cap run ios
```

#### Executar no Android:
```sh
npx cap run android
```

---

### 🔄 Live Reload

Para usar o modo de recarregamento ao vivo em dispositivos ou emuladores:

#### iOS:
```sh
ionic capacitor run ios -l --external
```

#### Android:
```sh
ionic capacitor run android -l --external
```

---

## 📚 Recursos Adicionais

- Documentação do [Ionic](https://ionicframework.com/docs).
- Documentação do [Vue 3](https://vuejs.org/guide/).

---

### Guia Passo a Passo: Criando um `.ipa` para Testes de Desenvolvimento

Este guia explica como construir e empacotar um aplicativo como um arquivo `.ipa` usando o Xcode, para fins de testes de desenvolvimento.

#### Requisitos
- Xcode instalado no macOS.
- Um projeto configurado e compilável no Xcode.

#### Passos

1. **Compile o Projeto**
   - Abra o Xcode e carregue seu projeto.
   - Pressione `⌘ + B` para compilar o projeto.
   - Certifique-se de que a compilação seja concluída sem erros.

2. **Localize o Arquivo `.app`**
   - Na barra lateral esquerda do Xcode, navegue até a pasta `Products`.
   - Dentro da pasta, encontre o arquivo `.app` correspondente ao seu projeto.
   - Clique com o botão direito no arquivo `.app` e selecione **Show in Finder** (Mostrar no Finder).

3. **Crie a Pasta Payload**
   - No Finder, crie uma nova pasta chamada **Payload**.
   - Arraste e solte o arquivo `.app` dentro da pasta **Payload**.

4. **Compacte a Pasta Payload**
   - Clique com o botão direito na pasta **Payload**.
   - Selecione **Compress** (Compactar).
   - Um arquivo `.zip` será criado.

5. **Renomeie o Arquivo**
   - Renomeie o arquivo `.zip` gerado, alterando a extensão `.zip` para `.ipa`.

Agora o arquivo `.ipa` está pronto para ser usado em testes de desenvolvimento.

### Nota
- Este método é destinado apenas para testes internos e não deve ser usado para distribuição na App Store. Para distribuição oficial, utilize o método **Archive** e assine o aplicativo com o certificado apropriado.