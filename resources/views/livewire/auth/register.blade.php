<div
    class="w-full sm:max-w-md mt-6 px-10 py-12 bg-glass shadow-2xl overflow-hidden sm:rounded-3xl transition-all duration-300 hover:shadow-indigo-500/20">
    <div class="mb-8 text-center">
        <h1
            class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">
            IELTS Master
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Create a new account</p>
    </div>

    <!-- Register Form -->
    <form wire:submit.prevent="register" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
            <div class="mt-1">
                <input wire:model="name" id="name" type="text" required autofocus
                    class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm transition-colors">
                @error('name') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
            <div class="mt-1">
                <input wire:model="email" id="email" type="email" required
                    class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm transition-colors">
                @error('email') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Are you a Student or
                Instructor?</label>
            <div class="mt-1">
                <select wire:model="role" id="role" required
                    class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm transition-colors">
                    <option value="student">Student</option>
                    <option value="instructor">Instructor</option>
                </select>
                @error('role') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
            <div class="mt-1">
                <input wire:model="password" id="password" type="password" required
                    class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm transition-colors">
                @error('password') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label for="password_confirmation"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
            <div class="mt-1">
                <input wire:model="password_confirmation" id="password_confirmation" type="password" required
                    class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm transition-colors">
                @error('password_confirmation') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div>
            <button type="submit"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                <svg wire:loading wire:target="register" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Register
            </button>
        </div>
    </form>

    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-transparent text-gray-500">
                    Already have an account?
                </span>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}"
                class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                Sign in here
            </a>
        </div>
    </div>
</div>