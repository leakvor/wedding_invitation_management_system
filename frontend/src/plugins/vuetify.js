import 'vuetify/styles';
import { createVuetify } from 'vuetify';

const weddingTheme = {
  dark: false,
  colors: {
    primary: '#0e3b2e',
    secondary: '#1c5a46',
    accent: '#c8a86b',
    surface: '#f7f1e7',
    background: '#e7ded0',
    success: '#2f7d5b',
    error: '#b55252',
    info: '#356d5c',
  },
};

export default createVuetify({
  theme: {
    defaultTheme: 'weddingTheme',
    themes: {
      weddingTheme,
    },
  },
});
