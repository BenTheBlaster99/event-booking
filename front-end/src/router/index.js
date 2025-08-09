import { createRouter, createWebHistory } from 'vue-router'
import SignUp from '@/views/SignUp.vue'
import SignIn from '@/views/SignIn.vue'
import Onboarding from '@/views/Onboarding.vue'
import MainPage from '@/views/MainPage.vue'
import SearchPage from '@/views/SearchPage.vue'
import Layout from '@/components/Layout.vue'
import EventDetail from '@/views/EventDetail.vue'
import MapPage from '@/views/MapPage.vue'
import AllEventsPage from '@/views/AllEventsPage.vue'
import EventsPage from '@/views/EventsPage.vue'

import ProfileEvents from '@/views/ProfileEvents.vue'
import ProfileReviews from '@/views/ProfileReviews.vue'
import ProfilePage from '@/views/ProfilePage.vue'
import NotificationPage from '@/views/NotificationPage.vue'
import ForgotPasswordPage from '@/views/ForgotPasswordPage.vue'
import ResetPasswordPage from '@/views/ResetPasswordPage.vue'
import ProfileEdit from '@/views/ProfileEdit.vue'
import ProfileBookmarks from '@/views/ProfileBookmarks.vue'
import ProfileSettings from '@/views/ProfileSettings.vue'
import CreateEventPage from '@/views/CreateEventPage.vue'
import ProfileAbout from '@/views/ProfileAbout.vue'

// Placeholder components for missing routes
const ComingSoon = { template: '<div class="p-4 text-center text-lg font-bold">Coming Soon!</div>' }
const EventsPlaceholder = {
  template: '<div class="p-4 text-center text-lg font-bold">Events Page</div>',
} // Renamed to avoid conflict
const MapPlaceholder = { template: '<div class="p-4 text-center text-lg font-bold">Map Page</div>' } // Renamed to avoid conflict

const routes = [
  {
    path: '/signin',
    name: 'SignIn',
    component: SignIn,
  },
  {
    path: '/signup',
    name: 'SignUp',
    component: SignUp,
  },
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: ForgotPasswordPage,
  },
  {
    path: '/reset-password',
    name: 'ResetPassword',
    component: ResetPasswordPage,
  },
  {
    path: '/event/:id',
    name: 'EventDetail',
    component: EventDetail,
  },
  {
    path: '/home',
    component: Layout,
    children: [
      {
        path: '', // Default child route for /home
        name: 'MainPage',
        component: MainPage,
      },
      {
        path: 'map',
        name: 'Map',
        component: MapPage,
      },
      {
        path: 'search',
        name: 'Search',
        component: SearchPage,
      },
      // Add routes for other bottom navigation items
      {
        path: 'events',
        name: 'Events',
        component: EventsPage, // Placeholder
      },

      {
        path: 'all-events',
        name: 'AllEvents',
        component: AllEventsPage,
      },
      {
        path: 'map',
        name: 'Map',
        component: MapPage, // Placeholder
      },
      { path: 'create-event', name: 'CreateEvent', component: CreateEventPage },

      {
        path: 'profile',

        component: ProfilePage, // Placeholder
        children: [
          {
            path: '',
            name: 'ProfileAbout',
            component: ProfileAbout,
          },
          {
            path: 'events',
            name: 'ProfileEvents',
            component: ProfileEvents,
          },
          {
            path: 'reviews',
            name: 'ProfileReviews',
            component: ProfileReviews,
          },
          {
            path: 'edit',
            name: 'ProfileEdit',
            component: ProfileEdit,
          },
          {
            path: 'settings',
            name: 'ProfileSettings',
            component: ProfileSettings,
          },
          {
            path: 'bookmarks',
            name: ' ProfileBookmarks',
            component: ProfileBookmarks,
          },
        ],
      },
      // Add routes for side menu items
      {
        path: 'messages',
        name: 'Messages',
        component: ComingSoon,
      },
      {
        path: '/notifications',
        name: 'Notifications',
        component: NotificationPage,
      },
      {
        path: 'calendar',
        name: 'Calendar',
        component: ComingSoon,
      },
      {
        path: 'bookmarks',
        name: 'Bookmarks',
        component: ComingSoon,
      },
      {
        path: 'contact',
        name: 'Contact',
        component: ComingSoon,
      },
      {
        path: 'settings',
        name: 'Settings',
        component: ComingSoon,
      },
      {
        path: 'help',
        name: 'Help',
        component: ComingSoon,
      },
      {
        path: 'logout', // For sign out, redirect to signin
        redirect: '/signin',
      },
    ],
  },
  {
    path: '/',
    name: 'Onboarding',
    component: Onboarding,
  },
  // Catch-all route for any undefined paths
  {
    path: '/:catchAll(.*)',
    redirect: '/', // Redirect to onboarding or a 404 page
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
