const API_URL = process.env.NEXT_PUBLIC_API_URL;

interface LoginData {
  email: string;
  password: string;
}

interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

interface ApiResponse {
  success: boolean;
  message: string;
  user?: any;
  token?: string;
}

export async function login(data: LoginData): Promise<ApiResponse> {
  try {
    const response = await fetch(`${API_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(data),
    });

    const result = await response.json();

    if (!response.ok) {
      throw  Error(result.message || 'Login failed');
    }

    return result;
  } catch (error: any) {
    console.error('Login error:', error);
    throw new Error(error.message || 'Network error. Please check if API is running.');
  }
}

export async function register(data: RegisterData): Promise<ApiResponse> {
  const response = await fetch(`${API_URL}/register`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify(data),
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'Registration failed');
  }

  return response.json();
}

export async function logout(token: string): Promise<ApiResponse> {
  const response = await fetch(`${API_URL}/logout`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Bearer ${token}`,
    },
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'Logout failed');
  }

  return response.json();
}

export async function getUser(token: string) {
  const response = await fetch(`${API_URL}/user`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Bearer ${token}`,
    },
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'Failed to get user');
  }

  return response.json();
}

