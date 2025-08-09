import {initializeApp} from 'firebase/app';
import {getFireStore} from 'firebase/firestore';

const firebaseConfig ={
    apiKey: '',
    authDomain: '',
    projectId: ' ',
    storageBucket: '',
    messagingSenderId :'',
    appId:''
};

const app = initializeApp(firebaseConfig);
const db = getFireStore(app);
export {db}