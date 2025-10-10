# SQLite Development Mode for Mage-OS

> **⚠️ WARNING**: This feature is for **DEVELOPMENT MODE ONLY**. Never use SQLite in production.

## Overview

This feature brings Laravel-style developer experience to Mage-OS by enabling SQLite as a database backend for local development. The goal is to go from zero to a working storefront in under 2 minutes with a single command.

## Current Status

**Stage 1: COMPLETE** ✅
- SQLite database adapter implementation
- Basic type mapping (MySQL → SQLite)
- Core adapter methods (describeTable, getIndexList, etc.)
- Connection factory and DI integration

**Stage 2-7: IN PROGRESS** 🚧
- Query translation layer
- `dev:serve` command
- Auto-install functionality
- Development command suite
- Extension compatibility

## Quick Start (Preview)

**Note**: Full functionality not yet available. This is Stage 1 implementation.

### Manual Configuration

To test the SQLite adapter, update your `app/etc/env.php`:

```php
'db' => [
    'connection' => [
        'default' => [
            'host' => '',
            'dbname' => 'var/dev.sqlite',  // SQLite database file
            'username' => '',
            'password' => '',
            'model' => 'mysql4',
            'engine' => 'innodb',
            'initStatements' => '',
            'active' => '1',
            'driver_options' => [
                'sqlite_aggressive_compat' => true  // Enable query rewriting
            ]
        ]
    ]
],
```

Then configure the connection adapter in `app/etc/di.xml`:

```xml
<type name="Magento\Framework\App\ResourceConnection\ConnectionFactory">
    <arguments>
        <argument name="typeMapping" xsi:type="array">
            <item name="pdo_sqlite" xsi:type="string">Magento\Framework\Model\ResourceModel\Type\Db\Pdo\Sqlite</item>
        </argument>
    </arguments>
</type>
```

## Architecture

### Core Components

1. **SQLite Adapter** (`Magento\Framework\DB\Adapter\Pdo\Sqlite`)
   - Extends MySQL adapter
   - Translates MySQL-specific SQL to SQLite
   - Handles type mapping and schema introspection

2. **Connection Adapter** (`Magento\Framework\Model\ResourceModel\Type\Db\Pdo\Sqlite`)
   - Implements `ConnectionAdapterInterface`
   - Manages SQLite connection lifecycle
   - Auto-creates database file in `var/dev.sqlite`

3. **Query Translation Layer** (Stage 2 - Coming Soon)
   - Runtime SQL rewriting
   - MySQL function compatibility
   - DDL statement translation

### Type Mapping

| MySQL Type | SQLite Type | Notes |
|-----------|-------------|-------|
| INT, BIGINT, SMALLINT | INTEGER | All int types → INTEGER |
| DECIMAL, NUMERIC, FLOAT | REAL | Numeric types → REAL |
| VARCHAR, TEXT, CHAR | TEXT | All text types → TEXT |
| BLOB, VARBINARY | BLOB | Binary data → BLOB |
| TIMESTAMP, DATETIME | TEXT/INTEGER | Flexible storage |

### SQLite Optimizations

The adapter automatically enables these PRAGMA settings:

```sql
PRAGMA journal_mode=WAL;        -- Better concurrency
PRAGMA synchronous=NORMAL;       -- Faster writes (dev-safe)
PRAGMA cache_size=10000;         -- 10MB cache
PRAGMA temp_store=MEMORY;        -- Temp tables in RAM
PRAGMA foreign_keys=ON;          -- FK enforcement
```

## Limitations

### Known SQLite Limitations

1. **No concurrent writes** - Single write transaction at a time
2. **Different full-text search** - Uses FTS5 instead of MATCH AGAINST
3. **No stored procedures** - Not supported
4. **Simplified transactions** - No savepoint names
5. **Type flexibility** - SQLite is more lenient with types

### Current Implementation Limitations (Stage 1)

- ❌ No automatic setup command yet
- ❌ Manual configuration required
- ❌ No query translation for complex MySQL syntax
- ❌ Extensions may fail without compatibility layer
- ❌ No dev command suite (`dev:serve`, `dev:reset`, etc.)

## Development Roadmap

See [IMPLEMENTATION_PLAN.md](../IMPLEMENTATION_PLAN.md) for the complete 7-stage roadmap.

### Upcoming Stages

**Stage 2**: Query Translation Layer (2 weeks)
- MySQL → SQLite SQL rewriting
- Function compatibility (CONCAT_WS, GROUP_CONCAT, IF, etc.)
- DDL translation

**Stage 3**: Dev Server Command (1 week)
- `bin/magento dev:serve` command
- PHP built-in server integration
- Routing and asset handling

**Stage 4**: Auto-Install (1 week)
- Detect missing database
- One-command installation
- Sensible defaults (admin/admin123, localhost:8000)

**Stage 5**: Dev Command Suite (2 weeks)
- `dev:reset` - Fresh installation
- `dev:seed` - Sample data
- `dev:snapshot` / `dev:restore` - Database snapshots
- `dev:check-extension` - Compatibility checker

**Stage 6**: Extension Compatibility (2 weeks)
- Query logging and analysis
- Community compatibility database
- Aggressive compatibility mode

**Stage 7**: Polish & Documentation (1 week)
- Performance optimizations
- Comprehensive documentation
- User testing

## Testing

### Manual Testing

1. Configure SQLite in `app/etc/env.php` (see Quick Start)
2. Run setup:install with SQLite:
   ```bash
   bin/magento setup:install \
       --db-host='' \
       --db-name='var/dev.sqlite' \
       --db-user='' \
       --db-password='' \
       --backend-frontname=admin \
       --admin-user=admin \
       --admin-password=admin123 \
       --admin-email=admin@example.com \
       --admin-firstname=Admin \
       --admin-lastname=User
   ```

3. Verify tables were created:
   ```bash
   sqlite3 var/dev.sqlite ".tables"
   ```

### Expected Behavior

✅ Tables should be created in SQLite format
✅ Basic CRUD operations should work
✅ Indexes and foreign keys should be created
⚠️ Some complex queries may fail (query translation not yet implemented)
⚠️ Extensions may break (compatibility layer not yet implemented)

## Contributing

This is an active development feature. Contributions welcome!

### Areas for Contribution

1. **Query Translation** - Help implement MySQL → SQLite SQL rewriting
2. **Extension Testing** - Test popular extensions for compatibility
3. **Performance** - Optimize queries for SQLite
4. **Documentation** - Improve setup guides and troubleshooting
5. **Testing** - Write integration and unit tests

### Reporting Issues

When reporting issues, please include:

- SQLite version: `sqlite3 --version`
- PHP version: `php -v`
- Error messages from `var/log/system.log`
- Query that failed (if applicable)
- Steps to reproduce

## FAQ

**Q: Can I use this in production?**
A: **NO**. This is explicitly for development mode only. SQLite is not suitable for production Magento workloads.

**Q: Will my SQLite dev database work with production MySQL?**
A: Development should be done with SQLite, but you should test against MySQL before deployment. Entity IDs and data should be compatible, but test thoroughly.

**Q: Why is this useful?**
A: It dramatically reduces the barrier to entry for new developers and speeds up local development by eliminating database setup complexity.

**Q: What about performance?**
A: Dev mode isn't about performance - it's about iteration speed. SQLite may be slower for complex queries, but setup is 15-30x faster.

**Q: Can I migrate my SQLite data to MySQL?**
A: A migration tool is planned for Stage 5. For now, data structures should be compatible but you'll need to export/import manually.

**Q: Does this work with Magento 2.4.x?**
A: This is being developed for Mage-OS (2.4-develop branch). Compatibility with Adobe Magento is not guaranteed.

## License

Same as Mage-OS core (OSL-3.0).

## Credits

Inspired by Laravel's seamless developer experience with `php artisan serve` and SQLite support.

Developed as part of the Mage-OS community initiative to improve developer experience.

---

**Project Status**: 🚧 Active Development - Stage 1 Complete
**Target Completion**: 3 months from start
**Next Milestone**: Query Translation Layer (Stage 2)
