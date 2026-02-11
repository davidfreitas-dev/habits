import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';

export default [
  // Configuração base do ESLint
  js.configs.recommended,
  
  // Configurações recomendadas para Vue 3 (strongly-recommended)
  ...pluginVue.configs['flat/strongly-recommended'],
  
  {
    // Arquivos a serem verificados
    files: ['**/*.{js,mjs,cjs,vue}'],
    
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        // Globais do navegador
        window: 'readonly',
        document: 'readonly',
        navigator: 'readonly',
        console: 'readonly',
        localStorage: 'readonly',
        sessionStorage: 'readonly',
        fetch: 'readonly',
        // Globais do Node (para configs)
        process: 'readonly',
        __dirname: 'readonly',
        module: 'readonly',
        require: 'readonly',
        // Capacitor
        Capacitor: 'readonly',
      },
    },
    
    rules: {
      // Regras migradas do seu .eslintrc.js
      'no-unused-vars': 'off',
      'vue/multi-word-component-names': 'off',
      'vue/no-deprecated-slot-attribute': 'off',
      'vue/no-multi-spaces': 'warn',
      'indent': ['error', 2],
      'semi': ['error', 'always'],
      'quotes': ['error', 'single'],
      'comma-spacing': ['error', { 
        before: false, 
        after: true 
      }],
      'vue/max-attributes-per-line': ['error', {
        'singleline': {
          'max': 2
        },      
        'multiline': {
          'max': 1
        }
      }],
      
      // Regras adicionais úteis
      'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
      'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
    },
  },
  
  {
    // Ignorar arquivos
    ignores: [
      'node_modules/**',
      'dist/**',
      'build/**',
      '.capacitor/**',
      'android/**',
      'ios/**',
      'coverage/**',
      '.cypress/**',
    ],
  },
];
