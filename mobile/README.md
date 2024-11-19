# Habits

## Technologies used:

- Ionic
- Vue 3

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
ionic serve
```

### Add iOS and Android

```sh
npx cap add ios
npx cap add android
```

### Sync Files

```sh
npx cap sync
```

### Running iOS and Android

```sh
npx cap run ios
npx cap run android
```

---

### Step-by-Step Guide: Creating an `.ipa` for Development Testing

This guide explains how to build and package an app as an `.ipa` file using Xcode for development testing purposes.

#### Requirements
- Xcode installed on macOS.
- A configured and buildable project in Xcode.

#### Steps

1. **Build the Project**
   - Open Xcode and load your project.
   - Press `⌘ + B` to build the project.
   - Ensure the build completes without errors.

2. **Locate the `.app` File**
   - In the left sidebar of Xcode, navigate to the `Products` folder.
   - Inside the folder, find the `.app` file corresponding to your project.
   - Right-click on the `.app` file and select **Show in Finder**.

3. **Create the Payload Folder**
   - In Finder, create a new folder named **Payload**.
   - Drag and drop the `.app` file into the **Payload** folder.

4. **Compress the Payload Folder**
   - Right-click on the **Payload** folder.
   - Select **Compress**.
   - A compressed `.zip` file will be created.

5. **Rename the File**
   - Rename the generated `.zip` file, changing the `.zip` extension to `.ipa`.

Now the `.ipa` file is ready to be used for development testing.

### Note
- This method is intended for internal testing only and should not be used for App Store distribution. For official distribution, use the **Archive** method and sign the app with the appropriate certificate.