import React from "react";
import AppBar from "@/Components/Partials/AppBar";

const Layout = ({ children, css = "px-4 md:px-8 lg:px-16 xl:px-24 pt-6", positionConfig = 'mx-auto' }) => {
    /**
     * Layout component that wraps page content
     * @param {ReactNode} children - Page content
     * @param {string} css - Custom CSS classes for padding/margin (default: "px-4 md:px-8 lg:px-16 xl:px-24 pt-6")
     * @param {string} positionConfig - CSS classes for content alignment (default: "mx-auto")
     */
    return (
        <div className="min-h-screen bg-gray-100">
            <AppBar />
            <main className={`flex-1 w-full mt-14 ${css.trim()}`}>
                <div className={`max-w-6xl ${positionConfig} w-full`}>
                    {children}
                </div>
            </main>
        </div>
    );
};

export default Layout;