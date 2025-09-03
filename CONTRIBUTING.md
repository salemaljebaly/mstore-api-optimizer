# Contributing to MStore API Optimizer

Thank you for your interest in contributing to MStore API Optimizer! This document provides guidelines for contributing to the project.

## 🤝 How to Contribute

### Reporting Bugs

Before creating a bug report, please:

1. **Check existing issues** to avoid duplicates
2. **Test with the latest version** of the plugin
3. **Disable other plugins** to isolate the issue
4. **Gather system information** (WordPress, WooCommerce, PHP versions)

When creating a bug report, include:

- **Clear description** of the issue
- **Steps to reproduce** the problem
- **Expected vs actual behavior**
- **System information** (versions, hosting environment)
- **Debug logs** if available
- **Screenshots** if applicable

### Suggesting Features

Feature requests are welcome! Please:

1. **Check existing feature requests** first
2. **Describe the use case** and problem it solves
3. **Provide detailed specifications** if possible
4. **Consider implementation complexity**

### Code Contributions

#### Setting Up Development Environment

1. **Clone the repository**
   ```bash
   git clone https://github.com/salemaljebaly/mstore-api-optimizer.git
   cd mstore-api-optimizer
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up WordPress development environment**
   - Local WordPress installation
   - WooCommerce plugin active
   - MStore API plugin active
   - WordPress debug logging enabled

#### Coding Standards

We follow WordPress coding standards:

- **PHP**: WordPress PHP Coding Standards
- **Documentation**: Inline comments for complex logic
- **Naming**: Clear, descriptive variable and function names
- **Security**: Proper sanitization and validation

#### Testing

Before submitting:

1. **Test thoroughly** with different cart sizes
2. **Verify compatibility** with various WooCommerce setups
3. **Check performance impact** using debug logs
4. **Ensure no PHP errors** or warnings

#### Pull Request Process

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make your changes**
   - Follow coding standards
   - Add appropriate comments
   - Update documentation if needed

4. **Test your changes**
   - Test with different scenarios
   - Verify no regressions
   - Check performance impact

5. **Commit your changes**
   ```bash
   git commit -m "Add feature: your feature description"
   ```

6. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

7. **Create a Pull Request**
   - Provide clear description
   - Reference related issues
   - Include testing details

## 🧪 Testing Guidelines

### Manual Testing

Test these scenarios:

1. **Small carts** (1-10 items)
2. **Medium carts** (11-50 items)
3. **Large carts** (51+ items)
4. **Different product types** (simple, variable, subscription)
5. **Various shipping methods**
6. **Multiple payment gateways**
7. **Different WooCommerce extensions**

### Performance Testing

Monitor these metrics:

- **Response time** for shipping_methods endpoint
- **Response time** for payment_methods endpoint
- **Memory usage** during processing
- **Database queries** count and time
- **Debug log output** for timing information

### Compatibility Testing

Test with:

- **WordPress versions**: 5.0, 5.5, 6.0, latest
- **WooCommerce versions**: 3.0, 4.0, 5.0, latest
- **PHP versions**: 7.4, 8.0, 8.1, 8.2
- **MStore API versions**: 4.0+

## 📝 Documentation

When contributing:

1. **Update README.md** if features change
2. **Update CHANGELOG.md** with your changes
3. **Add inline comments** for complex code
4. **Update FAQ** if needed

## 🔒 Security

If you discover security vulnerabilities:

1. **Do NOT create public issues**
2. **Email directly**: salemaljebaly@gmail.com
3. **Provide detailed information**
4. **Allow time for patching** before disclosure

## 💬 Communication

- **GitHub Issues**: Technical discussions
- **GitHub Discussions**: General questions and ideas
- **Email**: Direct communication with maintainers

## 📜 License

By contributing, you agree that your contributions will be licensed under the same license as the project (GPL v2 or later).

## 🙏 Recognition

Contributors will be:

- Listed in the plugin credits
- Mentioned in release notes
- Acknowledged in the README

Thank you for helping make MStore API Optimizer better! 🚀