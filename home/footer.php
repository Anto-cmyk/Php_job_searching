<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
        <link rel="stylesheet" href="../public/css/style.css">

</head>
<body>
    

<!-- FOOTER -->
<footer class="bg-gray-800 text-gray-200 mt-16">
    <div class="container mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
        
        <!-- About Section -->
        <div>
            <h2 class="text-xl font-bold mb-4 text-white">JobFinder</h2>
            <p class="text-gray-400 text-sm">JobFinder is your ultimate platform to find employment opportunities across Kenya. We connect employers with talent efficiently and securely.</p>
        </div>

        
         

        <!-- Contact Info -->
        <div>
            <h3 class="text-lg font-semibold mb-4 text-white">Contact Us</h3>
            <ul class="space-y-2 text-gray-400 text-sm">
                <li>Email: <a href="mailto:support@jobfinder.com" class="hover:text-blue-400">support@jobfinder.com</a></li>
                <li>Phone: <a href="tel:+254700000000" class="hover:text-blue-400">+254 700 000 000</a></li>
                <li>Address: Nairobi, Kenya</li>
            </ul>
            <div class="flex space-x-4 mt-4">
                <a href="#" class="hover:text-blue-400"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="hover:text-blue-400"><i class="fab fa-twitter"></i></a>
                <a href="#" class="hover:text-blue-400"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="hover:text-blue-400"><i class="fab fa-instagram"></i></a>
            </div>
        </div>

        <!-- Newsletter -->
        <div>
            <h3 class="text-lg font-semibold mb-4 text-white">Newsletter</h3>
            <p class="text-gray-400 text-sm mb-4">Subscribe to get the latest jobs and updates.</p>
            <form action="#" method="POST" class="flex flex-col space-y-2">
                <input type="email" name="email" placeholder="Your email" required
                       class="px-4 py-2 rounded-md text-gray-800 focus:outline-none">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition">Subscribe</button>
            </form>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-gray-700 mt-8">
        <div class="container mx-auto px-6 py-4 text-center text-gray-400 text-sm">
            &copy; <?php echo date('Y'); ?> JobFinder. All rights reserved. Designed with ❤️ in Kenya.
        </div>
    </div>
</footer>





</body>
</html>