/*
 * JasperReports - Free Java Reporting Library.
 * Copyright (C) 2001 - 2011 Jaspersoft Corporation. All rights reserved.
 * http://www.jaspersoft.com
 *
 * Unless you have purchased a commercial license agreement from Jaspersoft,
 * the following license terms apply:
 *
 * This program is part of JasperReports.
 *
 * JasperReports is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JasperReports is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with JasperReports. If not, see <http://www.gnu.org/licenses/>.
 */
package net.sf.jasperreports.crosstabs.fill;

import java.math.BigDecimal;
import java.math.BigInteger;
import java.util.HashMap;
import java.util.Map;

import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.fill.JRCalculable;

/**
 * Factory for percentage calculators.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRPercentageCalculatorFactory.java 5180 2012-03-29 13:23:12Z teodord $
 */
public final class JRPercentageCalculatorFactory
{
	private static final Map<String, JRPercentageCalculator> builtInCalculators;

	private static final Map<String, JRPercentageCalculator> cachedCalculators;

	static
	{
		builtInCalculators = new HashMap<String, JRPercentageCalculator>();
		builtInCalculators.put(Float.class.getName(), new FloatPercentageCalculator());
		builtInCalculators.put(Double.class.getName(), new DoublePercentageCalculator());
		builtInCalculators.put(Integer.class.getName(), new IntegerPercentageCalculator());
		builtInCalculators.put(Long.class.getName(), new LongPercentageCalculator());
		builtInCalculators.put(Short.class.getName(), new ShortPercentageCalculator());
		builtInCalculators.put(Byte.class.getName(), new BytePercentageCalculator());
		builtInCalculators.put(BigDecimal.class.getName(), new BigDecimalPercentageCalculator());
		builtInCalculators.put(BigInteger.class.getName(), new BigIntegerPercentageCalculator());

		cachedCalculators = new HashMap<String, JRPercentageCalculator>();
	}

	
	/**
	 * Checks whether a class has built-in percentage support.
	 * 
	 * @param valueClass the class
	 * @return whether the class has built-in percentage support
	 */
	public static boolean hasBuiltInCalculator(Class<?> valueClass)
	{
		return builtInCalculators.containsKey(valueClass.getName());
	}

	
	/**
	 * Returns a percentage calculator.
	 * <p>
	 * If the percentage calculator class is not null, it will be used to instantiate a percentage calculator.
	 * Otherwise, a built-in percentage calculator will be returned based on the value class.
	 * 
	 * @param percentageCalculatorClass the percentage calculator class
	 * @param valueClass the value class
	 * @return a percentage calculator for the percentage calculator class/value class
	 */
	public static JRPercentageCalculator getPercentageCalculator(Class<?> percentageCalculatorClass, Class<?> valueClass)
	{
		JRPercentageCalculator calculator;

		if (percentageCalculatorClass == null)
		{
			calculator = builtInCalculators.get(valueClass.getName());
			if (calculator == null)
			{
				throw new JRRuntimeException("Measure with type " + valueClass.getName() + " should specify a percentage calculator class.");
			}
		}
		else
		{
			calculator = cachedCalculators.get(percentageCalculatorClass.getName());
			
			if (calculator == null)
			{
				try
				{
					calculator = (JRPercentageCalculator) percentageCalculatorClass.newInstance();
					cachedCalculators.put(percentageCalculatorClass.getName(), calculator);
				}
				catch (InstantiationException e)
				{
					throw new JRRuntimeException("Error while creating percentage calculator instance of " + percentageCalculatorClass + ".", e);
				}
				catch (IllegalAccessException e)
				{
					throw new JRRuntimeException("Error while creating percentage calculator instance of " + percentageCalculatorClass + ".", e);
				}
			}
		}

		return calculator;
	}

	
	/**
	 * Percentage calculator for {@link Byte Byte} values.
	 */
	public static class BytePercentageCalculator implements JRPercentageCalculator
	{
		public Object calculatePercentage(JRCalculable value, JRCalculable total)
		{
			Byte totalVal = (Byte) total.getValue();
			Byte val = (Byte) value.getValue();
			byte percentage = 0;
			if (totalVal != null && totalVal.byteValue() != 0)
			{
				percentage = (byte) (100 * val.byteValue() / totalVal.byteValue());
			}

			return new Byte(percentage);
		}
	}

	
	/**
	 * Percentage calculator for {@link Short Short} values.
	 */
	public static class ShortPercentageCalculator implements JRPercentageCalculator
	{
		public Object calculatePercentage(JRCalculable value, JRCalculable total)
		{
			Short totalVal = (Short) total.getValue();
			Short val = (Short) value.getValue();
			short percentage = 0;
			if (totalVal != null && totalVal.shortValue() != 0)
			{
				percentage = (short) (100 * val.shortValue() / totalVal.shortValue());
			}

			return new Short(percentage);
		}
	}

	
	/**
	 * Percentage calculator for {@link Integer Integer} values.
	 */
	public static class IntegerPercentageCalculator implements JRPercentageCalculator
	{
		public Object calculatePercentage(JRCalculable value, JRCalculable total)
		{
			Integer totalVal = (Integer) total.getValue();
			Integer val = (Integer) value.getValue();
			int percentage = 0;
			if (totalVal != null && totalVal.intValue() != 0)
			{
				percentage = 100 * val.intValue() / totalVal.intValue();
			}

			return Integer.valueOf(percentage);
		}
	}

	
	/**
	 * Percentage calculator for {@link Long Long} values.
	 */
	public static class LongPercentageCalculator implements JRPercentageCalculator
	{
		public Object calculatePercentage(JRCalculable value, JRCalculable total)
		{
			Long totalVal = (Long) total.getValue();
			Long val = (Long) value.getValue();
			long percentage = 0L;
			if (totalVal != null && totalVal.longValue() != 0)
			{
				percentage = 100L * val.longValue() / totalVal.longValue();
			}

			return new Long(percentage);
		}
	}

	
	/**
	 * Percentage calculator for {@link Float Float} values.
	 */
	public static class FloatPercentageCalculator implements JRPercentageCalculator
	{
		public Object calculatePercentage(JRCalculable value, JRCalculable total)
		{
			Float totalVal = (Float) total.getValue();
			Float val = (Float) value.getValue();
			float percentage = 0f;
			if (totalVal != null && totalVal.floatValue() != 0)
			{
				percentage = 100f * val.floatValue() / totalVal.floatValue();
			}

			return new Float(percentage);
		}
	}

	/**
	 * Percentage calculator for {@link Double Double} values.
	 */
	public static class DoublePercentageCalculator implements JRPercentageCalculator
	{
		public Object calculatePercentage(JRCalculable value, JRCalculable total)
		{
			Double totalVal = (Double) total.getValue();
			Double val = (Double) value.getValue();
			double percentage = 0d;
			if (totalVal != null && totalVal.doubleValue() != 0)
			{
				percentage = 100d * val.doubleValue() / totalVal.doubleValue();
			}

			return new Double(percentage);
		}
	}

	
	/**
	 * Percentage calculator for {@link BigDecimal BigDecimal} values.
	 */
	public static class BigDecimalPercentageCalculator implements JRPercentageCalculator
	{
		public Object calculatePercentage(JRCalculable value, JRCalculable total)
		{
			BigDecimal totalVal = (BigDecimal) total.getValue();
			BigDecimal val = (BigDecimal) value.getValue();
			BigDecimal percentage;
			if (totalVal != null && totalVal.doubleValue() != 0)
			{
				percentage = val.multiply(BigDecimal.valueOf(100L)).divide(totalVal, BigDecimal.ROUND_HALF_UP);
			}
			else
			{
				percentage = BigDecimal.valueOf(0);
			}

			return percentage;
		}
	}

	
	/**
	 * Percentage calculator for {@link BigInteger BigInteger} values.
	 */
	public static class BigIntegerPercentageCalculator implements JRPercentageCalculator
	{
		public Object calculatePercentage(JRCalculable value, JRCalculable total)
		{
			BigInteger totalVal = (BigInteger) total.getValue();
			BigInteger val = (BigInteger) value.getValue();
			BigInteger percentage;
			if (totalVal != null && totalVal.doubleValue() != 0)
			{
				percentage = val.multiply(BigInteger.valueOf(100L)).divide(totalVal);
			}
			else
			{
				percentage = BigInteger.valueOf(0);
			}

			return percentage;
		}
	}
	
	
	private JRPercentageCalculatorFactory()
	{
	}
}
