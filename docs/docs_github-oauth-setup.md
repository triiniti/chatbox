# Setting Up a GitHub OAuth App for Triniiti ChatBox

To enable users to log in with their GitHub accounts, you need to register an OAuth application on GitHub and obtain a **Client ID** and **Client Secret**.

## Step 1: Go to GitHub Developer Settings

1. Log in to your GitHub account.
2. Navigate to **Settings** (click your profile icon in the top right, then “Settings”).
3. Scroll down to the **"Developer settings"** section in the left sidebar.
4. Click on **"OAuth Apps"**.

## Step 2: Register a New OAuth Application

1. Click the **"New OAuth App"** button.
2. Fill in the application details:
   - **Application name:**  
     `Triniiti ChatBox` (or your preferred name)
   - **Homepage URL:**  
     The URL where your app is hosted (e.g., `http://localhost` for local development, or your production domain).
   - **Authorization callback URL:**  
     The URL GitHub will redirect to after user authentication.  
     For local development, use something like:  
     `http://localhost/auth/callback`  
     (Replace with your actual app route if different.)

3. Click **"Register application"**.

## Step 3: Get Your Client ID and Client Secret

- After registration, you’ll be taken to your new OAuth app’s page.
- Here you’ll see your **Client ID** (copy this value).
- Click **"Generate a new client secret"** to reveal your **Client Secret** (copy this value as well).

> **Important:**  
> Keep your Client Secret private and never commit it to a public repository.

## Step 4: Add Credentials to Your App

Add your `CLIENT_ID` and `CLIENT_SECRET` to your `.env` file (or your deployment environment):

```env
GITHUB_OAUTH_CLIENT_ID=your_client_id_here
GITHUB_OAUTH_CLIENT_SECRET=your_client_secret_here
```

## Step 5: Configure Redirect URLs

Ensure the **Authorization callback URL** in your GitHub OAuth app settings matches the redirect URI used in your application’s OAuth flow.  
For example:  
- Local development: `http://localhost/auth/callback`
- Production: `https://your-domain.com/auth/callback`

---

**References:**
- [GitHub Docs: Creating an OAuth App](https://docs.github.com/en/developers/apps/building-oauth-apps/creating-an-oauth-app)
- [GitHub Docs: OAuth App Settings](https://docs.github.com/en/developers/apps/building-oauth-apps/authorizing-oauth-apps)
