// Bootstrap (jQuery, Axios, Stimulus, etc.)
import './bootstrap.js';

// Global CSS
import './styles/app.css';
import './styles/form.css';
import './styles/view.css';

// Page-specific CSS (if you want all styles in one JS)
import './scripts/dashboard.js';      // dashboard page JS + imports dashboard.css
import './scripts/orderindex.js';     // order index page JS + imports orderindex.css
import './scripts/usersindex.js';     // users index page JS + imports usersindex.css
import './scripts/orderedit.js';      // order edit page JS + imports orderedit.css
import './scripts/serviceindex.js';   // service index page JS + imports serviceindex.css

console.log('App JS loaded: all page scripts imported!');
