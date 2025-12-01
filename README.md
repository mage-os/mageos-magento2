<p align="center">
  <a href="https://mage-os.org">
    <img src="https://raw.githubusercontent.com/mage-os/mageos-magento2/refs/heads/main/lib/web/images/mage-os-logo.svg" alt="Mage-OS">
  </a>
</p>

[![Website](https://img.shields.io/badge/Website-mage--os.org-orange?style=for-the-badge)](https://mage-os.org)
[![Discord](https://img.shields.io/badge/Discord-Join%20Community-5865F2?style=for-the-badge&logo=discord&logoColor=white)](https://mage-os.org/discord-channel/)
[![License](https://img.shields.io/badge/License-OSL--3.0-blue?style=for-the-badge)](https://opensource.org/licenses/OSL-3.0)

## 🚀 What is Mage-OS?

Mage-OS is an open-source eCommerce platform. It is an **independent distribution** of Magento™ Open Source, designed to be fully compatible with the existing ecosystem while moving faster to fix bugs, merge improvements, and modernize the technical stack.

Mage-OS focuses on:

- **Innovation:** Implementing new features and performance improvements that the community actually needs.
- **Velocity:** Reducing the time and barriers from contribution to release.
- **Compatibility:** Ensuring existing Magento 2 themes and extensions continue to work seamlessly.
- **Open Governance:** Managed by the non-profit **Mage-OS Association**, ensuring the platform serves our users' best interests.

[**Learn more at mage-os.org →**](https://mage-os.org)

---

## 📦 About This Repository

The `mageos-magento2` repository is the **canonical source code** for the Mage-OS distribution.

This codebase is based on Magento Open Source, with independent management and revisions. It includes community-contributed fixes, performance enhancements, and features that may not exist yet (or ever) in Magento itself.

**This repository is:**

- The core code used to generate Mage-OS Composer packages.
- The place where core bug fixes and improvements are merged.

**It is _not_:**

- A “kitchen sink” for all community extensions (see [Mage-OS Lab](#-mage-os-lab--innovation) instead).
- The project root for your specific store (you should install via Composer).

---

## 🛠️ Getting Started

### Installation

The recommended way to install Mage-OS is via Composer. This ensures you get the correct dependency tree and can easily upgrade in the future.

```
composer create-project --repository-url=https://repo.mage-os.org/ mage-os/project-community-edition .
```

For detailed installation instructions, server configuration, and troubleshooting, visit the **[Official Mage-OS Documentation](https://devdocs.mage-os.org/docs/main)**.

### Upgrading

Mage-OS maintains compatibility with upstream Magento versions while layering on improvements. We only support our latest release branch at any time, so we advise always staying up to date. Review the release notes before upgrading.

- **[View Release Notes](https://mage-os.org/releases/)**
- **[Upgrade Guide](https://devdocs.mage-os.org/docs/main)**

### System Requirements

Mage-OS generally follows the same system requirements (PHP, MySQL, OpenSearch) as the equivalent version of Magento Open Source. Refer to the documentation for up-to-date details:

- **[System Requirements](https://devdocs.mage-os.org/docs/main)**

---

## 🤝 Contributing to the Core

Mage-OS is built by people like you. Contributions of all sizes are welcome, anything from typo fixes to major architectural improvements.

If you have any questions or concerns first, please open an issue or join our community chat.

### Reporting Issues & Security Concerns

- Use **[GitHub Issues](https://github.com/mage-os/mageos-magento2/issues)** for bugs and feature requests.
    - Include clear reproduction steps, affected versions, and environment details.
- For security-sensitive issues, please refer to:
    - **[Security Policy](SECURITY.md)**
    - Or contact: `security@mage-os.org`

### Contributing Code (Pull Requests)

- Open PRs directly against this repository.
- Keep changes focused and well-scoped.
- Include tests where appropriate and avoid unnecessary breaking changes.
- Follow the **[Coding Standards](https://devdocs.mage-os.org/docs/main/magento-2-coding-standards)**

---

## 🔌 Extensions, Themes & Integrations

Mage-OS is designed to work with the broader Magento ecosystem.

- Extensions, themes, and integrations are typically maintained in **separate Composer packages**.
- They may live under the Mage-OS organization or in vendor-owned repositories.
- For experimental or early-stage ideas, see **Mage-OS Lab**.

If you want to build an extension or theme that aligns closely with Mage-OS goals, we can help you with best practices and guidance.

---

## 🔬 Mage-OS Lab & Innovation

### 🧪 Mage-OS Lab

- **[Mage-OS Lab @ GitHub →](https://github.com/mage-os-lab)**  
  The innovation hub for experimental projects and forward-looking ideas. This is where new tools, modules, and approaches are created and tested.

### 🌱 Graduated Lab Projects

- **[Graduated Mage-OS Lab Projects →](https://github.com/mage-os?q=topic%3Amage-os-lab)**  
  A curated set of projects that started in Mage-OS Lab and have matured into stable, ecosystem-ready solutions. Many of these are a bundled part of the Mage-OS Distribution.

---

## 🧰 Related Repositories & Tools

Mage-OS includes tooling to support distribution, CI, and ecosystem workflows.

- **Mage-OS GitHub Organization**  
  - https://github.com/mage-os
  - Contains all Mage-OS repositories and related tools, and mirror copies of each Magento repository.

- **Release & Mirror Generator**  
  - **Repo:** https://github.com/mage-os/generate-mirror-repo-js/  
  - Generates Composer packages, repositories, and releases from this codebase.

- **Magento / Mage-OS GitHub Actions**  
  - **Repo:** https://github.com/mage-os/github-actions  
  - Reusable CI building blocks for Mage-OS and Magento 2 projects.

For more projects, visit the [community modules directory](https://mage-os.org/community-modules-directory/).

---

## 💬 Community & Support

Join the global Mage-OS community:

- **Discord (chat & support):** https://mage-os.org/discord-channel/  
- **Website:** https://mage-os.org  
- **Events:** https://mage-os.org/community/magento-events/  

Our active Discord server has channels for #tech discussion, chat, #help, and more. You can browse at the link above.

---

## 🏛️ Governance & Membership

Mage-OS is a project of the **Mage-OS Association**, a non-profit organization that provides open governance and long-term stewardship.

- Decisions are made transparently with community input.
- Memberships fund infrastructure, development, and community initiatives.

Learn more and become a member:

- **[Membership Levels](https://mage-os.org/about/mage-os-membership/)**  
- **[About the Association](https://mage-os.org/about/)**  

---

## 🙌 Contributors & Acknowledgements

Mage-OS exists thanks to:

- Individual contributors from around the world.
- Agencies, extension vendors, and hosting providers investing their time, money, and expertise.
- The extensive past and ongoing work of Adobe and the Magento Open Source community.

You can view the current list of contributors here:

- **[Contributors Graph](https://github.com/mage-os/mageos-magento2/graphs/contributors)**

---

## ⚖️ License & Legal

The code in this repository is dual-licensed under the **[Open Software License (OSL 3.0)](LICENSE.txt)** and **[Academic Free License (AFL 3.0)](LICENSE_AFL.txt)**, in line with the original Magento Open Source licensing.

_Magento® is a registered trademark of Adobe Inc. Mage-OS is not affiliated with Adobe or Magento Open Source in any way._
