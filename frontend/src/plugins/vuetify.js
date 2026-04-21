import 'vuetify/styles';
import { createVuetify } from 'vuetify';

const weddingTheme = {
  dark: false,
  colors: {
    primary: '#184d3b',
    secondary: '#2d6a4f',
    accent: '#dbe7dd',
    surface: '#f7fbf8',
    background: '#edf5ef',
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
