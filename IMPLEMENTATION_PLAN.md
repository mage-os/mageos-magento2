# Installer Bug Fixes - Implementation Plan

## Stage 1: Add env.php/config.php backup logic
**Goal**: Backup existing Magento config files before installation
**Location**: MagentoInstallationStage
**Success Criteria**: 
- Existing env.php and config.php are backed up with timestamp
- Installation proceeds without collision
- Backups can be restored manually if needed
**Status**: Not Started

## Stage 2: Fix config save on failure
**Goal**: Save config context when installation fails at any stage
**Location**: StageNavigator.navigate() method
**Success Criteria**:
- Config is saved before entering MagentoInstallationStage (point of no return)
- Config is saved when any stage returns abort/failure
- User can resume installation after failure
**Tests**:
- Simulate failure at MagentoInstallationStage
- Verify .mageos-install-config.json exists
- Verify resume prompt appears on next run
**Status**: Not Started

## Stage 3: Investigate "can't go back" issue
**Goal**: Verify and document back navigation behavior
**Location**: Config stages and StageNavigator
**Success Criteria**:
- Document which stages allow back navigation
- Verify Laravel Prompts supports ctrl+u for back
- Add explicit "Go back" option if needed
**Tests**:
- Test back navigation during config collection
- Verify message accuracy
**Status**: Not Started

## Stage 4: Update messaging
**Goal**: Make messages accurate about back navigation
**Location**: WelcomeStage
**Success Criteria**:
- Message accurately reflects when you CAN go back
- Clear instructions on how to go back
- No false promises
**Status**: Not Started
