import React, { useState } from 'react';
import LandingPage from './pages/LandingPage';
import Dashboard from './pages/Dashboard';

export default function App() {
  const initialPage = window.location.pathname.startsWith('/workhub/dashboard')
    ? 'dashboard'
    : 'landing';
  const [currentPage, setCurrentPage] = useState(initialPage);

  if (currentPage === 'dashboard') {
    return <Dashboard />;
  }

  return <LandingPage onOpenDashboard={() => {
    window.history.pushState({}, '', '/workhub/dashboard');
    setCurrentPage('dashboard');
  }} />;
}
