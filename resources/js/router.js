import Home from "./components/pages/Home";
import Screens from "./components/pages/Screens";
import UploadVideo from "./components/pages/UploadVideo";
import Checkout from "./components/pages/Checkout";
import Campaign from "./components/pages/Campaign";
import FindCampaign from "./components/pages/FindCampaign";

export default [
    { path: '/', component: Home },
    { path: '/screens', component: Screens, name: 'screens' },
    { path: '/video', component: UploadVideo, name: 'video' },
    { path: '/checkout', component: Checkout, name: 'checkout' },
    { path: '/campaign', component: FindCampaign, name: 'find-campaign' },
    { path: '/campaign/:email/:code', component: Campaign, name: 'campaign' },
]
