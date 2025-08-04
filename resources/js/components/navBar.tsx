import { Link, usePage } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';

const Navbar = () => {
    const [scrolled, setScrolled] = useState(false);
    const [showLogin, setShowLogin] = useState(false);
    const [showMobileMenu, setShowMobileMenu] = useState(false);

    const { logos, contacto, auth, carrito } = usePage().props;

    const [userMenu, setUserMenu] = useState(false);

    const { user } = auth;

    const loginRef = useRef(null);

    useEffect(() => {
        // Close login menu when clicking outside
        const handleClickOutside = (event) => {
            if (loginRef.current && !loginRef.current.contains(event.target)) {
                setShowLogin(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []);

    useEffect(() => {
        // Check if it's home page or not
        const location = window.location.pathname;
        const isHome = location === '/';

        if (isHome) {
            window.addEventListener('scroll', () => {
                setScrolled(window.scrollY > 0);
            });
        } else {
            setScrolled(true);
        }

        return () => {
            window.removeEventListener('scroll', () => {
                setScrolled(window.scrollY > 0);
            });
        };
    }, []);

    const defaultLinks = [
        { title: 'Empresa', href: '/empresa' },
        { title: 'Productos', href: '/productos' },
        { title: 'Calidad', href: '/calidad' },
        { title: 'Lanzamientos', href: '/lanzamientos' },
        { title: 'Contacto', href: '/contacto' },
    ];

    const privateLinks = [
        { title: 'Productos', href: '/privada/productos' },
        { title: 'Carrito', href: '/privada/carrito' },
        { title: 'Mis pedidos', href: '/privada/pedidos' },

        /* { title: 'Cuenta corriente', href: '/privada/cuenta-corriente' }, */
        { title: 'Lista de precios', href: '/privada/lista-de-precios' },
    ];

    const linksToRender = window.location.pathname.includes('privada') ? privateLinks : defaultLinks;

    return (
        <div
            className={`sticky top-0 z-50 flex h-[100px] w-full flex-col bg-white transition-colors duration-300 max-sm:h-auto ${scrolled ? 'bg-white shadow-md' : 'bg-transparent'}`}
        >
            {/* Main Navbar Content */}
            <div className="mx-auto flex h-full w-[1200px] items-center justify-between max-sm:h-[60px] max-sm:w-full max-sm:px-4">
                <a href="/">
                    <img src={logos?.logo_principal} className="max-h-[83px] transition-all duration-300 max-sm:h-8" alt="Logo" />
                </a>

                {/* Desktop Menu */}
                <div className="relative hidden items-center gap-20 md:flex">
                    {linksToRender.map((link) => (
                        <Link
                            key={link.href}
                            href={link.href}
                            className={`hover:text-primary-orange text-sm text-black transition-colors duration-300 ${
                                window.location.pathname === link.href ? 'font-bold' : ''
                            }`}
                        >
                            {link.title}
                        </Link>
                    ))}
                </div>
                <div className="relative flex flex-col">
                    <button
                        onClick={() => setShowLogin(true)}
                        type="button"
                        className="border-primary-orange text-primary-orange hover:bg-primary-orange rounded-lg border px-4 py-2 text-sm font-bold transition duration-300 hover:text-white max-sm:px-2 max-sm:text-xs"
                    >
                        {auth?.user?.name}
                    </button>
                    {showLogin && <div className="fixed inset-0 bg-black/50 transition-all duration-300" />}
                    {showLogin && (
                        <div
                            ref={loginRef}
                            className="absolute top-12 right-0 z-50 flex h-fit w-fit flex-col items-center gap-5 rounded-lg border bg-white p-5 transition-all duration-300 max-sm:top-8 max-sm:p-3"
                        >
                            <p className="max-sm:text-sm">Bienvenido, {auth?.user?.name}!</p>
                            {user?.rol === 'vendedor' && (
                                <Link
                                    href={route('borrarCliente')}
                                    className="bg-primary-orange flex h-[48px] w-[328px] items-center justify-center rounded-lg font-bold text-white max-sm:h-[40px] max-sm:w-[250px] max-sm:text-sm"
                                >
                                    Elegir cliente
                                </Link>
                            )}
                            <div className="flex flex-col">
                                <Link
                                    method="post"
                                    href={route('logout')}
                                    className="flex h-[48px] w-[328px] items-center justify-center rounded-lg bg-red-500 font-bold text-white max-sm:h-[40px] max-sm:w-[250px] max-sm:text-sm"
                                >
                                    Cerrar sesion
                                </Link>
                            </div>
                        </div>
                    )}
                </div>

                {/* Mobile Menu Button */}
                <button
                    onClick={() => setShowMobileMenu(!showMobileMenu)}
                    className="h-6 w-6 flex-col items-center justify-center space-y-1 max-sm:flex md:hidden"
                >
                    <span
                        className={`block h-0.5 w-5 bg-current transition-all duration-300 ${showMobileMenu ? 'translate-y-1.5 rotate-45' : ''}`}
                    ></span>
                    <span className={`block h-0.5 w-5 bg-current transition-all duration-300 ${showMobileMenu ? 'opacity-0' : ''}`}></span>
                    <span
                        className={`block h-0.5 w-5 bg-current transition-all duration-300 ${showMobileMenu ? '-translate-y-1.5 -rotate-45' : ''}`}
                    ></span>
                </button>
            </div>

            {/* Mobile Menu */}
            {showMobileMenu && (
                <div className="absolute top-full left-0 z-40 w-full bg-white shadow-lg md:hidden">
                    <div className="flex flex-col py-4">
                        {linksToRender.map((link) => (
                            <Link
                                key={link.href}
                                href={link.href}
                                className={`hover:text-primary-orange border-b border-gray-100 px-4 py-3 text-sm transition-colors duration-300 ${
                                    window.location.pathname === link.href ? 'font-bold' : ''
                                }`}
                                onClick={() => setShowMobileMenu(false)}
                            >
                                {link.title}
                            </Link>
                        ))}
                        {window.location.pathname.includes('privada') && (
                            <Link
                                href={'/privada/carrito'}
                                className="flex items-center gap-2 border-b border-gray-100 px-4 py-3"
                                onClick={() => setShowMobileMenu(false)}
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#0072c6"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    className="lucide lucide-shopping-cart-icon lucide-shopping-cart"
                                >
                                    <circle cx="8" cy="21" r="1" />
                                    <circle cx="19" cy="21" r="1" />
                                </svg>
                                <span className="text-sm">Carrito</span>
                            </Link>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
};

export default Navbar;
